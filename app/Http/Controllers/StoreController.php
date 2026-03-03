<?php

namespace App\Http\Controllers;

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
        $cart = [
            ['id' => 1, 'name' => 'iPhone 15 Pro Max', 'brand' => 'Apple', 'price' => 1199, 'qty' => 1, 'image' => 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=200&h=200&fit=crop'],
            ['id' => 3, 'name' => 'ThinkPad X1 Carbon', 'brand' => 'Lenovo', 'price' => 1499, 'qty' => 1, 'image' => 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=200&h=200&fit=crop'],
        ];
        $subtotal = collect($cart)->sum(fn ($i) => $i['price'] * $i['qty']);
        $shipping = $subtotal >= 99 ? 0 : 9.99;
        $total = $subtotal + $shipping;

        return view('store.cart', [
            'cart' => $cart,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total,
        ]);
    }
}
