<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TechStore') – {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/store.css') }}">
</head>
<body>
    <nav class="store-nav">
        <a href="{{ url('/') }}" class="logo">TechStore</a>
        <div class="nav-center">
            <a href="{{ url('/') }}">Home</a>
            <a href="{{ url('/store?cat=phones') }}">Phones</a>
            <a href="{{ url('/store?cat=laptops') }}">Laptops</a>
            <a href="{{ url('/store#brands') }}">Brands</a>
            <a href="{{ url('/store?deals=1') }}">Deals</a>
        </div>
        <div class="nav-right">
            <div class="search-autocomplete">
                <input type="text" id="search-input" placeholder="Search products..." autocomplete="off">
                <div class="dropdown" id="search-dropdown"></div>
            </div>
            <button type="button" class="icon-btn" aria-label="Wishlist">♡</button>
            <a href="{{ url('/store/cart') }}" class="icon-btn cart-icon-link" id="cart-icon-link" aria-label="Cart">
                &#128722;
                <span class="cart-badge" id="cart-badge" @if(($cartItemCount ?? 0) < 1) style="display:none;" @endif>{{ ($cartItemCount ?? 0) > 99 ? '99+' : ($cartItemCount ?? 0) }}</span>
            </a>

            @auth
            <div class="user-info">
                <span class="icon-btn">👤</span>
                <span class="user-name">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-btn-simple">Logout</button>
                </form>
            </div>
            @else
                <a href="{{ route('login') }}" class="icon-btn">Login</a>
                <a href="{{ route('register') }}" class="icon-btn">Register</a>
            @endauth

            <button type="button" class="menu-toggle" aria-label="Menu" id="menu-toggle">
                <span></span><span></span><span></span>
            </button>
        </div>
    </nav>

    @yield('content')

    <footer class="store-footer">
        <div class="footer-grid">
            <div>
                <h4>About Us</h4>
                <ul>
                    <li><a href="#">Our Story</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#">Press</a></li>
                </ul>
            </div>
            <div>
                <h4>Customer Service</h4>
                <ul>
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="#">Shipping & Returns</a></li>
                    <li><a href="#">FAQ</a></li>
                </ul>
            </div>
            <div>
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="{{ url('/') }}">Home</a></li>
                    <li><a href="{{ url('/?cat=phones') }}">Phones</a></li>
                    <li><a href="{{ url('/?cat=laptops') }}">Laptops</a></li>
                    <li><a href="{{ url('/store/cart') }}">Cart</a></li>
                </ul>
            </div>
            <div>
                <h4>Contact</h4>
                <ul>
                    <li>support@techstore.com</li>
                    <li>+1 800 123 4567</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; {{ date('Y') }} TechStore. All rights reserved.
        </div>
    </footer>

    <script src="{{ asset('js/store.js') }}"></script>
    @stack('scripts')
</body>
</html>
