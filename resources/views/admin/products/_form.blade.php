@php
    $isEdit = isset($product);
@endphp

<div style="display:grid; grid-template-columns: 1fr 1fr; gap: 12px;">
    <div style="grid-column: 1 / -1;">
        <label style="display:block; font-weight:800; margin-bottom:6px;">Name</label>
        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required
               style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:12px;">
        @error('name')<div style="color:#b91c1c; font-size:13px; margin-top:6px;">{{ $message }}</div>@enderror
    </div>

    <div>
        <label style="display:block; font-weight:800; margin-bottom:6px;">Brand</label>
        <input type="text" name="brand" value="{{ old('brand', $product->brand ?? '') }}" required
               style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:12px;">
        @error('brand')<div style="color:#b91c1c; font-size:13px; margin-top:6px;">{{ $message }}</div>@enderror
    </div>

    <div>
        <label style="display:block; font-weight:800; margin-bottom:6px;">Category</label>
        <select name="category" required style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:12px;">
            @foreach(['phones' => 'Phones', 'laptops' => 'Laptops'] as $val => $label)
                <option value="{{ $val }}" @selected(old('category', $product->category ?? '') === $val)>{{ $label }}</option>
            @endforeach
        </select>
        @error('category')<div style="color:#b91c1c; font-size:13px; margin-top:6px;">{{ $message }}</div>@enderror
    </div>

    <div>
        <label style="display:block; font-weight:800; margin-bottom:6px;">Price (RM)</label>
        <input type="number" step="0.01" name="price" value="{{ old('price', $product->price ?? '') }}" required
               style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:12px;">
        @error('price')<div style="color:#b91c1c; font-size:13px; margin-top:6px;">{{ $message }}</div>@enderror
    </div>

    <div>
        <label style="display:block; font-weight:800; margin-bottom:6px;">Rating (0-5)</label>
        <input type="number" step="0.1" name="rating" value="{{ old('rating', $product->rating ?? '0.0') }}" required
               style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:12px;">
        @error('rating')<div style="color:#b91c1c; font-size:13px; margin-top:6px;">{{ $message }}</div>@enderror
    </div>

    <div>
        <label style="display:block; font-weight:800; margin-bottom:6px;">Stock</label>
        <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" required
               style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:12px;">
        @error('stock')<div style="color:#b91c1c; font-size:13px; margin-top:6px;">{{ $message }}</div>@enderror
    </div>

    <div style="grid-column: 1 / -1;">
        <label style="display:block; font-weight:800; margin-bottom:6px;">Image URL</label>
        <input type="text" name="image" value="{{ old('image', $product->image ?? '') }}" required
               style="width:100%; padding:12px 14px; border:1px solid #e5e7eb; border-radius:12px;">
        @error('image')<div style="color:#b91c1c; font-size:13px; margin-top:6px;">{{ $message }}</div>@enderror
        <div style="color:#6b7280; font-size:12px; margin-top:6px;">Paste the product image link (same format your store already uses).</div>
    </div>
</div>

