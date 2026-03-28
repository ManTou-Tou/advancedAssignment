@extends('layouts.store')

@section('title', 'Cart')

@section('content')
<div class="cart-page">
    <div class="cart-items">
        <h2 style="margin-bottom: 24px;">Shopping Cart</h2>
        @if(session('message'))
            <p style="color: var(--accent); margin-bottom: 16px;">{{ session('message') }}</p>
        @endif
        @if(session('error'))
            <p style="color: #c00; margin-bottom: 16px;">{{ session('error') }}</p>
        @endif
        @forelse($cart as $item)
        <div class="cart-item">
            <img src="{{ str_starts_with($item['image'] ?? '', 'http') ? $item['image'] : asset($item['image']) }}" alt="{{ $item['name'] }}">
            <div class="details">
                <div class="name">{{ $item['name'] }}</div>
                <div style="font-size:14px;color: var(--text-secondary);">{{ $item['brand'] }}</div>
                <div class="price">${{ number_format($item['price'], 0) }}</div>
                <div class="qty-remove">
                    <form action="{{ route('store.cart.update') }}" method="post" style="display:inline;">
                        @csrf
                        <input type="hidden" name="cart_item_id" value="{{ $item['cart_item_id'] }}">
                        <select name="quantity" onchange="this.form.submit()">
                            @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" {{ (int)$item['qty'] === $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </form>
                    <form action="{{ route('store.cart.remove') }}" method="post" style="display:inline;">
                        @csrf
                        <input type="hidden" name="cart_item_id" value="{{ $item['cart_item_id'] }}">
                        <button type="submit" class="remove">Remove</button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <p style="color: var(--text-secondary); padding: 32px 0;">Your cart is empty. <a href="{{ url('/') }}">Continue shopping</a>.</p>
        @endforelse
    </div>
    @if(count($cart) > 0)
    <div class="cart-summary">
        <h3>Order Summary</h3>
        <div class="row"><span>Subtotal</span><span>${{ number_format($subtotal, 2) }}</span></div>
        <div class="row"><span>Shipping</span><span>{{ $shipping > 0 ? '$' . number_format($shipping, 2) : 'Free' }}</span></div>
        <div class="row total"><span>Total</span><span>${{ number_format($total, 2) }}</span></div>
        <a href="{{ route('store.checkout') }}" class="btn-checkout" style="display:block;text-align:center;line-height:48px;text-decoration:none;color:#fff;">Proceed to Checkout</a>
    </div>
    @endif
</div>
@endsection
