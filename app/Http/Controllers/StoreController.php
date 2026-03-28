<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    /**
     * Convert DB row (stdClass) to array for views.
     */
    private function toArray($row): array
    {
        return (array) $row;
    }

    /**
     * Get current cart session id (creates session if needed).
     */
    private function getCartSessionId(): string
    {
        if (!session()->has('_cart_token')) {
            session()->put('_cart_token', session()->getId());
        }
        return session()->get('_cart_token');
    }

    /**
     * Total units sold per product (from completed order lines).
     */
    private function getSoldCountsByProductId(array $productIds): array
    {
        if ($productIds === []) {
            return [];
        }
        $rows = DB::table('order_items')
            ->whereIn('product_id', $productIds)
            ->groupBy('product_id')
            ->selectRaw('product_id, SUM(quantity) as sold')
            ->get();

        return $rows->pluck('sold', 'product_id')->map(fn ($v) => (int) $v)->all();
    }

    private function attachSoldAndStock(array $productRow, array $soldMap): array
    {
        $id = (int) $productRow['id'];
        $productRow['sold'] = (int) ($soldMap[$id] ?? 0);
        $productRow['stock'] = (int) ($productRow['stock'] ?? 0);

        return $productRow;
    }

    public function home(Request $request)
    {
        $query = DB::table('products')->orderBy('created_at', 'desc');

        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }
        if ($request->filled('cat') && in_array($request->cat, ['phones', 'laptops'])) {
            $query->where('category', $request->cat);
        }

        $products = $query->get()->map(fn ($r) => $this->toArray($r));
        $ids = $products->pluck('id')->map(fn ($id) => (int) $id)->all();
        $soldMap = $this->getSoldCountsByProductId($ids);
        $products = $products->map(fn ($p) => $this->attachSoldAndStock($p, $soldMap));

        $featured_products = $products->take(8)->values()->all();
        $best_sellers = $products->sortByDesc('rating')->take(4)->values()->all();

        return view('store.home', [
            'featured_products' => $featured_products,
            'best_sellers' => $best_sellers,
        ]);
    }

    public function product(int $id)
    {
        $row = DB::table('products')->where('id', $id)->first();
        if (!$row) {
            abort(404);
        }

        $product = $this->toArray($row);
        $soldMap = $this->getSoldCountsByProductId([(int) $product['id']]);
        $product = $this->attachSoldAndStock($product, $soldMap);
        $product['images'] = [
            $product['image'],
            $product['image'],
            $product['image'],
        ];
        $product['storages'] = ['128GB', '256GB', '512GB'];
        $product['colors'] = ['Black', 'Silver', 'Blue'];

        return view('store.product', ['product' => $product]);
    }

    public function cart(Request $request)
    {
        $sessionId = $this->getCartSessionId();

        $items = DB::table('cart_items')
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->where('cart_items.session_id', $sessionId)
            ->select(
                'cart_items.id as cart_item_id',
                'cart_items.product_id',
                'cart_items.quantity as qty',
                'products.name',
                'products.brand',
                'products.price',
                'products.image',
                'products.stock'
            )
            ->get()
            ->map(fn ($r) => $this->toArray($r))
            ->all();

        $subtotal = collect($items)->sum(fn ($i) => (float) $i['price'] * (int) $i['qty']);
        $shipping = $subtotal >= 99 ? 0 : 9.99;
        $total = $subtotal + $shipping;

        return view('store.cart', [
            'cart' => $items,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total,
        ]);
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'nullable|integer|min:1|max:99',
        ]);

        $sessionId = $this->getCartSessionId();
        $productId = (int) $request->product_id;
        $quantity = (int) ($request->quantity ?? 1);

        $product = DB::table('products')->where('id', $productId)->first();
        $stock = (int) ($product->stock ?? 0);
        if ($stock < 1) {
            return $this->cartStockError($request, 'This product is out of stock.');
        }

        $existing = DB::table('cart_items')
            ->where('session_id', $sessionId)
            ->where('product_id', $productId)
            ->first();

        $currentInCart = $existing ? (int) $existing->quantity : 0;
        $newTotal = $currentInCart + $quantity;
        if ($newTotal > $stock) {
            return $this->cartStockError($request, 'Not enough stock. Only ' . $stock . ' available.');
        }

        if ($existing) {
            DB::table('cart_items')
                ->where('id', $existing->id)
                ->update(['quantity' => $newTotal, 'updated_at' => now()]);
        } else {
            DB::table('cart_items')->insert([
                'session_id' => $sessionId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $cartCount = (int) DB::table('cart_items')->where('session_id', $sessionId)->sum('quantity');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Added to cart',
                'cart_count' => $cartCount,
            ]);
        }

        return back()->with('message', 'Added to cart');
    }

    private function cartStockError(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => $message], 422);
        }
        return back()->with('error', $message);
    }

    public function removeFromCart(Request $request)
    {
        $request->validate([
            'cart_item_id' => 'required|integer',
        ]);

        $sessionId = $this->getCartSessionId();
        $deleted = DB::table('cart_items')
            ->where('id', $request->cart_item_id)
            ->where('session_id', $sessionId)
            ->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => (bool) $deleted]);
        }
        return redirect()->route('store.cart')->with('message', $deleted ? 'Item removed' : 'Item not found');
    }

    public function updateCartQuantity(Request $request)
    {
        $request->validate([
            'cart_item_id' => 'required|integer',
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $sessionId = $this->getCartSessionId();
        $line = DB::table('cart_items')
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->where('cart_items.id', $request->cart_item_id)
            ->where('cart_items.session_id', $sessionId)
            ->select('products.stock')
            ->first();

        if (!$line) {
            return redirect()->route('store.cart')->with('message', 'Item not found');
        }

        $stock = (int) ($line->stock ?? 0);
        if ((int) $request->quantity > $stock) {
            return redirect()->route('store.cart')->with('error', 'Only ' . $stock . ' in stock for this product.');
        }

        $updated = DB::table('cart_items')
            ->where('id', $request->cart_item_id)
            ->where('session_id', $sessionId)
            ->update(['quantity' => $request->quantity, 'updated_at' => now()]);

        if ($request->expectsJson()) {
            return response()->json(['success' => (bool) $updated]);
        }
        return redirect()->route('store.cart')->with('message', 'Cart updated');
    }

    public function checkout(Request $request)
    {
        $sessionId = $this->getCartSessionId();
        $items = DB::table('cart_items')
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->where('cart_items.session_id', $sessionId)
            ->select(
                'cart_items.product_id',
                'cart_items.quantity as qty',
                'products.name',
                'products.brand',
                'products.price',
            )
            ->get()
            ->map(fn ($r) => $this->toArray($r))
            ->all();

        if (empty($items)) {
            return redirect()->route('store.cart')->with('message', 'Your cart is empty.');
        }

        $subtotal = collect($items)->sum(fn ($i) => (float) $i['price'] * (int) $i['qty']);
        $shipping = $subtotal >= 99 ? 0 : 9.99;
        $total = $subtotal + $shipping;

        return view('store.checkout', [
            'cart' => $items,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total,
        ]);
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:tng,maybank,public_bank',
        ]);

        $sessionId = $this->getCartSessionId();
        $items = DB::table('cart_items')
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->where('cart_items.session_id', $sessionId)
            ->select(
                'cart_items.product_id',
                'cart_items.quantity as qty',
                'products.name',
                'products.price',
                'products.stock',
            )
            ->get();

        if ($items->isEmpty()) {
            return redirect()->route('store.cart')->with('message', 'Your cart is empty.');
        }

        $subtotal = $items->sum(fn ($i) => (float) $i->price * (int) $i->qty);
        $shipping = $subtotal >= 99 ? 0 : 9.99;
        $total = $subtotal + $shipping;

        try {
            $orderId = DB::transaction(function () use ($items, $sessionId, $request, $subtotal, $shipping, $total) {
                foreach ($items as $row) {
                    $product = DB::table('products')
                        ->where('id', $row->product_id)
                        ->lockForUpdate()
                        ->first();
                    $available = (int) ($product->stock ?? 0);
                    if (!$product || $available < (int) $row->qty) {
                        throw new \RuntimeException('Insufficient stock for: ' . $row->name . ' (only ' . $available . ' left).');
                    }
                }

                $orderId = DB::table('orders')->insertGetId([
                    'session_id' => $sessionId,
                    'subtotal' => $subtotal,
                    'shipping' => $shipping,
                    'total' => $total,
                    'status' => 'placed',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach ($items as $row) {
                    DB::table('order_items')->insert([
                        'order_id' => $orderId,
                        'product_id' => $row->product_id,
                        'product_name' => $row->name,
                        'price' => $row->price,
                        'quantity' => $row->qty,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    DB::table('products')
                        ->where('id', $row->product_id)
                        ->decrement('stock', (int) $row->qty);
                }

                DB::table('payments')->insert([
                    'order_id' => $orderId,
                    'payment_method' => $request->payment_method,
                    'amount' => $total,
                    'status' => 'successful',
                    'reference' => 'ORD-' . $orderId . '-' . now()->format('YmdHis'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('cart_items')->where('session_id', $sessionId)->delete();

                return $orderId;
            });
        } catch (\Throwable $e) {
            return redirect()->route('store.checkout')->with('error', 'Order failed: ' . $e->getMessage());
        }

        return redirect()->route('store.order.confirmation', ['id' => $orderId]);
    }

    public function orderConfirmation(int $id)
    {
        $sessionId = $this->getCartSessionId();
        $order = DB::table('orders')->where('id', $id)->where('session_id', $sessionId)->first();
        if (!$order) {
            abort(404);
        }
        $order = $this->toArray($order);
        $order['items'] = DB::table('order_items')->where('order_id', $id)->get()->map(fn ($r) => $this->toArray($r))->all();
        $payment = DB::table('payments')->where('order_id', $id)->first();
        $order['payment_method'] = $payment ? $this->paymentMethodLabel($payment->payment_method) : '—';
        $order['payment_reference'] = $payment->reference ?? '—';

        return view('store.order-confirmation', ['order' => $order]);
    }

    public function billPdf(int $id)
    {
        $sessionId = $this->getCartSessionId();
        $order = DB::table('orders')->where('id', $id)->where('session_id', $sessionId)->first();
        if (!$order) {
            abort(404);
        }
        $order = $this->toArray($order);
        $order['items'] = DB::table('order_items')->where('order_id', $id)->get()->map(fn ($r) => $this->toArray($r))->all();
        $payment = DB::table('payments')->where('order_id', $id)->first();
        $order['payment_method'] = $payment ? $this->paymentMethodLabel($payment->payment_method) : '—';
        $order['payment_reference'] = $payment->reference ?? '—';

        $pdf = Pdf::loadView('store.bill-pdf', ['order' => $order]);
        return $pdf->download('bill-order-' . $id . '.pdf');
    }

    private function paymentMethodLabel(string $method): string
    {
        return match ($method) {
            'tng' => 'Touch \'n Go (TNG)',
            'maybank' => 'Online Banking - Maybank',
            'public_bank' => 'Online Banking - Public Bank',
            default => $method,
        };
    }
}
