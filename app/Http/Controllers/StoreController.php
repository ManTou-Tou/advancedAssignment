<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $query = DB::table('products');

        // search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('brand', 'like', '%' . $search . '%')
                ->orWhere('category', 'like', '%' . $search . '%');
            });
        }

        // filter by brand
        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }

        // filter by category
        if ($request->filled('cat') && in_array($request->cat, ['phones', 'laptops'])) {
            $query->where('category', $request->cat);
        }

        // sort
        if ($request->sort === 'price_low') {
            $query->orderBy('price', 'asc');
        } elseif ($request->sort === 'price_high') {
            $query->orderBy('price', 'desc');
        } elseif ($request->sort === 'rating') {
            $query->orderBy('rating', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $products = $query->get()->map(fn ($r) => $this->toArray($r));
        $ids = $products->pluck('id')->map(fn ($id) => (int) $id)->all();
        $soldMap = $this->getSoldCountsByProductId($ids);
        $products = $products->map(fn ($p) => $this->attachSoldAndStock($p, $soldMap));

        $featured_products = $products->values()->all();
        $best_sellers = $products->sortByDesc('rating')->take(4)->values()->all();

        $brands = DB::table('products')->select('brand')->distinct()->pluck('brand')->all();

        return view('store.home', [
            'featured_products' => $featured_products,
            'best_sellers' => $best_sellers,
            'brands' => $brands,
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
        if (!Gate::allows('place-order')) {
            return redirect()->route('login')->with('error', 'Please login to checkout.');
        }

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
            'delivery_name' => 'required|string|max:255',
            'delivery_phone' => 'required|string|max:40',
            'delivery_address_line1' => 'required|string|max:255',
            'delivery_address_line2' => 'nullable|string|max:255',
            'delivery_city' => 'required|string|max:100',
            'delivery_state' => 'required|string|max:100',
            'delivery_postcode' => 'required|string|max:20',
            'delivery_country' => 'required|string|max:100',
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
                    'user_id' => Auth::guard('web')->id(),
                    'subtotal' => $subtotal,
                    'shipping' => $shipping,
                    'total' => $total,
                    'status' => 'placed',
                    'delivery_name' => $request->delivery_name,
                    'delivery_phone' => $request->delivery_phone,
                    'delivery_address_line1' => $request->delivery_address_line1,
                    'delivery_address_line2' => $request->delivery_address_line2,
                    'delivery_city' => $request->delivery_city,
                    'delivery_state' => $request->delivery_state,
                    'delivery_postcode' => $request->delivery_postcode,
                    'delivery_country' => $request->delivery_country,
                    'delivery_status' => 'pending',
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
                Log::info('Inserting payment', ['order_id' => $orderId]);
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
        $userId = Auth::guard('web')->id();

        $orderQuery = DB::table('orders')->where('id', $id);
        if ($userId) {
            $orderQuery->where('user_id', $userId);
        } else {
            $orderQuery->where('session_id', $sessionId);
        }

        $order = $orderQuery->first();
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
        $userId = Auth::guard('web')->id();

        $orderQuery = DB::table('orders')->where('id', $id);
        if ($userId) {
            $orderQuery->where('user_id', $userId);
        } else {
            $orderQuery->where('session_id', $sessionId);
        }

        $order = $orderQuery->first();
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

    public function myOrders()
    {
        $userId = Auth::guard('web')->id();
        if (!$userId) {
            return redirect()->route('login');
        }

        $orders = DB::table('orders')
            ->where('user_id', $userId)
            ->orderByDesc('id')
            ->limit(50)
            ->get()
            ->map(fn ($r) => $this->toArray($r))
            ->all();

        return view('store.my-orders', ['orders' => $orders]);
    }

    public function myOrderDetails(int $id)
    {
        $userId = Auth::guard('web')->id();
        if (!$userId) {
            return redirect()->route('login');
        }

        $order = DB::table('orders')->where('id', $id)->where('user_id', $userId)->first();
        if (!$order) {
            abort(404);
        }

        if (!Gate::allows('view-own-orders', $order)) {
            abort(403, 'You are not authorized to view this order.');
        }

        $order = $this->toArray($order);
        $order['items'] = DB::table('order_items')
            ->where('order_id', $id)
            ->get()
            ->map(fn ($r) => $this->toArray($r))
            ->all();

        return view('store.my-order-details', ['order' => $order]);
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

    public function favorite()
    {
        $sessionId = $this->getCartSessionId();

        $favorites = DB::table('favorites')
            ->join('products', 'favorites.product_id', '=', 'products.id')
            ->where('favorites.session_id', $sessionId)
            ->select(
                'favorites.id as favorite_id',
                'favorites.product_id',
                'products.name',
                'products.brand',
                'products.price',
                'products.image'
            )
            ->get()
            ->map(fn ($r) => $this->toArray($r))
            ->all();

        return view('store.favorite', [
            'favorites' => $favorites,
        ]);
    }

    public function removeFromFavorite(Request $request)
    {
        $request->validate([
            'favorite_id' => 'required|integer',
        ]);

        $sessionId = $this->getCartSessionId();

        $deleted = DB::table('favorites')
            ->where('id', $request->favorite_id)
            ->where('session_id', $sessionId)
            ->delete();

        return redirect()->route('store.favorite')->with('message', $deleted ? 'Item removed from favorite' : 'Item not found');
    }

    public function addToFavorite(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
        ]);

        $sessionId = $this->getCartSessionId();
        $productId = (int) $request->product_id;

        $exists = DB::table('favorites')
            ->where('session_id', $sessionId)
            ->where('product_id', $productId)
            ->first();

        if (!$exists) {
            DB::table('favorites')->insert([
                'session_id' => $sessionId,
                'product_id' => $productId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('message', 'Added to favorite');
    }

    public function payOrder($id)
{
    $sessionId = $this->getCartSessionId();

    $order = DB::table('orders')
        ->where('id', $id)
        ->where('session_id', $sessionId)
        ->first();

    if (!$order) {
        abort(404);
    }

    DB::table('orders')
        ->where('id', $id)
        ->update(['status' => 'processing']);

    $success = rand(0, 1);

    DB::table('orders')
        ->where('id', $id)
        ->update([
            'status' => $success ? 'paid' : 'failed'
        ]);

    DB::table('payments')->insert([
        'order_id' => $id,
        'payment_method' => 'simulated',
        'amount' => $order->total,
        'status' => $success ? 'successful' : 'failed',
        'reference' => 'SIM-' . $id . '-' . now()->format('YmdHis'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect()->route('store.order.confirmation', ['id' => $id]);
}
}
