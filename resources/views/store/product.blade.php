@extends('layouts.store')

@section('title', $product['name'])

@section('content')
<div class="product-detail">
    <div class="product-gallery">
        <div class="main-image">
            <img src="{{ str_starts_with($product['images'][0] ?? '', 'http') ? $product['images'][0] : asset($product['images'][0]) }}" alt="{{ $product['name'] }}" id="main-img" style="width:100%;height:100%;object-fit:contain;">
        </div>
        <div class="thumbnails">
            @foreach($product['images'] as $i => $img)
            <img src="{{ str_starts_with($img ?? '', 'http') ? $img : asset($img) }}" alt="" data-index="{{ $i }}" class="{{ $i === 0 ? 'active' : '' }}">
            @endforeach
        </div>
    </div>
    <div class="product-info">
        <h1>{{ $product['name'] }}</h1>
        <div class="brand-name">{{ $product['brand'] }}</div>
        <div class="rating">★★★★☆ {{ $product['rating'] }}</div>
        <div class="product-sold-line" style="font-size:15px;color:var(--text-secondary);margin-bottom:8px;">{{ number_format($product['sold'] ?? 0) }} sold</div>
        <div class="price">${{ number_format($product['price'], 0) }}</div>
        @if(($product['stock'] ?? 0) < 1)
            <p style="color:#c00;font-weight:600;margin:12px 0;">Out of stock</p>
        @else
            <p style="color:var(--text-secondary);margin:12px 0;">{{ $product['stock'] }} in stock</p>
        @endif

        <div class="options">
            <label>Storage</label>
            <div class="option-btns" id="storage-options">
                @foreach($product['storages'] as $s)
                <button type="button" class="{{ $loop->first ? 'active' : '' }}">{{ $s }}</button>
                @endforeach
            </div>
        </div>
        <div class="options">
            <label>Color</label>
            <div class="option-btns" id="color-options">
                @foreach($product['colors'] as $c)
                <button type="button" class="{{ $loop->first ? 'active' : '' }}">{{ $c }}</button>
                @endforeach
            </div>
        </div>

        <div class="qty">
            <label>Quantity</label>
            <input type="number" id="qty" name="quantity" value="1" min="1" max="{{ min(99, max(1, (int)($product['stock'] ?? 0))) }}" {{ ($product['stock'] ?? 0) < 1 ? 'disabled' : '' }}>
        </div>

        @if(($product['stock'] ?? 0) < 1)
            <p class="btn-add-cart" style="opacity:.5;cursor:not-allowed;text-align:center;">Out of stock</p>
        @else
        <form action="{{ route('store.cart.add') }}" method="post" id="add-to-cart-form" class="js-add-to-cart-form">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product['id'] }}">
            <input type="hidden" name="quantity" id="qty-hidden" value="1">
            <button type="submit" class="btn-add-cart">Add to Cart</button>
        </form>
        <a href="{{ url('/store/cart') }}" class="btn-buy-now" style="display:inline-block;text-align:center;box-sizing:border-box;">Buy Now</a>
        @endif

        <div class="product-tabs">
            <div class="tab-head">
                <button type="button" class="active" data-tab="desc">Description</button>
                <button type="button" data-tab="specs">Specifications</button>
                <button type="button" data-tab="reviews">Reviews</button>
            </div>
            <div id="tab-desc">
                <p style="color: var(--text-secondary);">Premium {{ $product['brand'] }} {{ $product['name'] }}. Built for performance and durability. Official warranty included.</p>
            </div>
            <div id="tab-specs" style="display:none;">
                <p style="color: var(--text-secondary);">Display, processor, memory, and storage specs vary by configuration. See manufacturer site for full details.</p>
            </div>
            <div id="tab-reviews" style="display:none;">
                <p style="color: var(--text-secondary);">Customer reviews will appear here.</p>
            </div>
        </div>
    </div>
</div>

<div class="sticky-cart" id="sticky-cart">
    <div>
        <strong>{{ $product['name'] }}</strong>
        <span style="color: var(--accent); font-weight: 600;">${{ number_format($product['price'], 0) }}</span>
    </div>
    @if(($product['stock'] ?? 0) < 1)
        <span class="btn-add-cart" style="max-width:200px;opacity:.5;cursor:not-allowed;">Out of stock</span>
    @else
    <form action="{{ route('store.cart.add') }}" method="post" class="js-add-to-cart-form" style="display:flex;">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product['id'] }}">
        <input type="hidden" name="quantity" id="qty-sticky" value="1">
        <button type="submit" class="btn-add-cart" style="max-width: 200px;">Add to Cart</button>
    </form>
    @endif
</div>

@push('scripts')
<script>
(function() {
    var main = document.getElementById('main-img');
    document.querySelectorAll('.thumbnails img').forEach(function(thumb) {
        thumb.addEventListener('click', function() {
            document.querySelectorAll('.thumbnails img').forEach(function(t) { t.classList.remove('active'); });
            thumb.classList.add('active');
            main.src = thumb.src;
        });
    });
    document.querySelectorAll('.option-btns button').forEach(function(btn) {
        btn.addEventListener('click', function() {
            this.parentElement.querySelectorAll('button').forEach(function(b) { b.classList.remove('active'); });
            this.classList.add('active');
        });
    });
    document.querySelectorAll('.tab-head button').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.tab-head button').forEach(function(b) { b.classList.remove('active'); });
            this.classList.add('active');
            var tab = this.getAttribute('data-tab');
            ['desc','specs','reviews'].forEach(function(id) {
                var el = document.getElementById('tab-' + id);
                if (el) el.style.display = id === tab ? 'block' : 'none';
            });
        });
    });
    window.addEventListener('scroll', function() {
        var sticky = document.getElementById('sticky-cart');
        if (sticky) sticky.classList.toggle('visible', window.scrollY > 500);
    });
    var qtyInput = document.getElementById('qty');
    var qtyHidden = document.getElementById('qty-hidden');
    var qtySticky = document.getElementById('qty-sticky');
    if (qtyInput && qtyHidden) {
        qtyInput.addEventListener('change', function() {
            var v = Math.max(1, Math.min(99, parseInt(this.value, 10) || 1));
            qtyHidden.value = v;
            if (qtySticky) qtySticky.value = v;
        });
    }
})();
</script>
@endpush
@endsection
