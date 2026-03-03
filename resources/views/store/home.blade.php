@extends('layouts.store')

@section('title', 'Home')

@section('content')
{{-- 1. Hero Slider --}}
<section class="hero">
    <div class="hero-slide active">
        <div class="hero-content">
            <h2>Latest Apple iPhone Collection</h2>
            <p>Premium design, powerful performance. Discover the newest iPhones.</p>
            <a href="{{ url('/store?brand=apple&cat=phones') }}" class="btn-primary">Shop Now</a>
        </div>
        <img src="https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=600&h=400&fit=crop" alt="Apple iPhone" class="hero-img">
    </div>
    <div class="hero-slide">
        <div class="hero-content">
            <h2>Powerful Lenovo Laptops for Productivity</h2>
            <p>Engineered for work and creativity. ThinkPad and IdeaPad.</p>
            <a href="{{ url('/store?brand=lenovo&cat=laptops') }}" class="btn-primary">Shop Now</a>
        </div>
        <img src="https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=600&h=400&fit=crop" alt="Lenovo Laptop" class="hero-img">
    </div>
    <div class="hero-slide">
        <div class="hero-content">
            <h2>Honor Smartphones – Smart Performance</h2>
            <p>Innovation and value. Flagship features at a great price.</p>
            <a href="{{ url('/store?brand=honor&cat=phones') }}" class="btn-primary">Shop Now</a>
        </div>
        <img src="https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=600&h=400&fit=crop" alt="Honor Phone" class="hero-img">
    </div>
    <div class="hero-dots" id="hero-dots"></div>
</section>

{{-- 2. Shop by Brand --}}
<section class="store-section" id="brands">
    <div class="section-title">
        <h2>Shop by Brand</h2>
        <p>Apple, Lenovo, Honor – premium electronics</p>
    </div>
    <div class="brand-grid">
        <a href="{{ url('/store?brand=apple') }}" class="brand-card">
            <div style="font-size:48px;color:#111;">🍎</div>
            <span class="brand-name">Apple</span>
        </a>
        <a href="{{ url('/store?brand=lenovo') }}" class="brand-card">
            <div style="font-size:48px;color:#111;">💻</div>
            <span class="brand-name">Lenovo</span>
        </a>
        <a href="{{ url('/store?brand=honor') }}" class="brand-card">
            <div style="font-size:48px;color:#111;">📱</div>
            <span class="brand-name">Honor</span>
        </a>
    </div>
</section>

{{-- 3. Featured Products --}}
<section class="store-section" style="background: var(--bg-light);">
    <div class="section-title">
        <h2>Featured Products</h2>
        <p>Handpicked smartphones and laptops</p>
    </div>
    <div class="toolbar">
        <div class="search-autocomplete">
            <input type="text" placeholder="Search..." id="filter-search" style="width:200px;">
        </div>
        <div style="display:flex;gap:12px;">
            <select id="filter-brand">
                <option value="">All Brands</option>
                <option value="apple">Apple</option>
                <option value="lenovo">Lenovo</option>
                <option value="honor">Honor</option>
            </select>
            <select id="filter-sort">
                <option value="newest">Newest</option>
                <option value="price-low">Price: Low to High</option>
                <option value="price-high">Price: High to Low</option>
                <option value="bestselling">Best Selling</option>
            </select>
        </div>
    </div>
    <div class="product-grid" id="featured-products">
        @foreach($featured_products as $p)
        <article class="product-card">
            <a href="{{ url('/store/product/' . $p['id']) }}">
                <div class="product-image">
                    <img src="{{ str_starts_with($p['image'] ?? '', 'http') ? $p['image'] : asset($p['image']) }}" alt="{{ $p['name'] }}">
                </div>
            </a>
            <div class="product-body">
                <div class="brand-tag">{{ $p['brand'] }}</div>
                <a href="{{ url('/store/product/' . $p['id']) }}"><div class="product-name">{{ $p['name'] }}</div></a>
                <div class="product-rating">★★★★☆ {{ $p['rating'] }}</div>
                <div class="product-price">${{ number_format($p['price'], 0) }}</div>
                <div class="product-actions">
                    <button type="button" class="btn-cart">Add to Cart</button>
                    <button type="button" class="wishlist-btn" aria-label="Wishlist">♡</button>
                </div>
            </div>
        </article>
        @endforeach
    </div>
</section>

{{-- 4. Category Banners --}}
<section class="store-section">
    <div class="category-banners">
        <a href="{{ url('/store?cat=phones') }}" class="category-banner" style="background: linear-gradient(135deg, #1a1a1a 0%, #333 100%);">
            <div class="overlay">
                <h3>Smartphones</h3>
                <p style="margin:0;opacity:.9;">Apple, Lenovo, Honor</p>
                <span class="btn-primary">Shop Now</span>
            </div>
        </a>
        <a href="{{ url('/store?cat=laptops') }}" class="category-banner" style="background: linear-gradient(135deg, #2c2c2c 0%, #444 100%);">
            <div class="overlay">
                <h3>Laptops</h3>
                <p style="margin:0;opacity:.9;">Powerful & portable</p>
                <span class="btn-primary">Shop Now</span>
            </div>
        </a>
    </div>
</section>

{{-- 5. Best Sellers --}}
<section class="store-section" style="background: var(--bg-light);">
    <div class="section-title">
        <h2>Best Sellers</h2>
        <p>Most popular this month</p>
    </div>
    <div class="product-grid">
        @foreach($best_sellers as $p)
        <article class="product-card">
            <a href="{{ url('/store/product/' . $p['id']) }}">
                <div class="product-image">
                    <img src="{{ str_starts_with($p['image'] ?? '', 'http') ? $p['image'] : asset($p['image']) }}" alt="{{ $p['name'] }}">
                </div>
            </a>
            <div class="product-body">
                <div class="brand-tag">{{ $p['brand'] }}</div>
                <a href="{{ url('/store/product/' . $p['id']) }}"><div class="product-name">{{ $p['name'] }}</div></a>
                <div class="product-rating">★★★★☆ {{ $p['rating'] }}</div>
                <div class="product-price">${{ number_format($p['price'], 0) }}</div>
                <div class="product-actions">
                    <button type="button" class="btn-cart">Add to Cart</button>
                    <button type="button" class="wishlist-btn" aria-label="Wishlist">♡</button>
                </div>
            </div>
        </article>
        @endforeach
    </div>
</section>

{{-- 6. Why Choose Us --}}
<section class="store-section">
    <div class="section-title">
        <h2>Why Choose Us</h2>
        <p>Premium service, trusted by thousands</p>
    </div>
    <div class="why-grid">
        <div class="why-card">
            <div class="icon">🚚</div>
            <h4>Fast Delivery</h4>
            <p>Free shipping on orders over $99</p>
        </div>
        <div class="why-card">
            <div class="icon">🛡️</div>
            <h4>1 Year Warranty</h4>
            <p>Official manufacturer warranty</p>
        </div>
        <div class="why-card">
            <div class="icon">🔒</div>
            <h4>Secure Payment</h4>
            <p>SSL encrypted checkout</p>
        </div>
        <div class="why-card">
            <div class="icon">💬</div>
            <h4>24/7 Support</h4>
            <p>We're here when you need us</p>
        </div>
    </div>
</section>

{{-- 7. Newsletter --}}
<section class="newsletter">
    <div class="newsletter-inner">
        <h3>Subscribe for Exclusive Deals</h3>
        <p>Get early access to sales and new arrivals. No spam.</p>
        <form class="newsletter-form" action="#" method="post">
            @csrf
            <input type="email" name="email" placeholder="Your email" required>
            <button type="submit">Subscribe</button>
        </form>
    </div>
</section>
@endsection
