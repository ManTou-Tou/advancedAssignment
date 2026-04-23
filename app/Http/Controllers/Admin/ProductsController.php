<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductsController extends Controller
{
    public function index(): View
    {
        if (!Gate::allows('manage-products')) {
            abort(403, 'You are not authorized to view this page.');
        }

        $products = Product::query()
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.products.index', ['products' => $products]);
    }

    public function create(): View
    {
        return view('admin.products.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', ['product' => $product]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validated($request);
        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if (!Gate::allows('manage-products')) {
            abort(403);
        }
        
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:100'],
            'category' => ['required', 'in:phones,laptops'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'rating' => ['required', 'numeric', 'min:0', 'max:5'],
            'image' => ['required', 'string'],
            'stock' => ['required', 'integer', 'min:0', 'max:1000000'],
        ]);
    }
}

