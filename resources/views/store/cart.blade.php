@extends('layouts.store')

@section('title', 'Cart')

@section('content')
<div class="cart-page">
    <div class="cart-items">
        <h2 style="margin-bottom: 24px;">Shopping Cart</h2>
        @foreach($cart as $item)
        <div class="cart-item">
            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}">
            <div class="details">
                <div class="name">{{ $item['name'] }}</div>
                <div style="font-size:14px;color: var(--text-secondary);">{{ $item['brand'] }}</div>
                <div class="price">${{ number_format($item['price'], 0) }}</div>
                <div class="qty-remove">
                    <select data-id="{{ $item['id'] }}">
                        @for($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}" {{ $item['qty'] == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                    <button type="button" class="remove">Remove</button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="cart-summary">
        <h3>Order Summary</h3>
        <div class="row"><span>Subtotal</span><span>${{ number_format($subtotal, 2) }}</span></div>
        <div class="row"><span>Shipping</span><span>{{ $shipping > 0 ? '$' . number_format($shipping, 2) : 'Free' }}</span></div>
        <div class="row total"><span>Total</span><span>${{ number_format($total, 2) }}</span></div>
        <button type="button" class="btn-checkout">Proceed to Checkout</button>
    </div>
</div>
@endsection
