@extends('layouts.store')

@section('title', 'Favorite')

@section('content')
<div class="cart-page">
    <div class="cart-items">
        <h2 style="margin-bottom: 24px;">My Favorite</h2>

        @if(session('message'))
            <p style="color: var(--accent); margin-bottom: 16px;">{{ session('message') }}</p>
        @endif

        @if(session('error'))
            <p style="color: #c00; margin-bottom: 16px;">{{ session('error') }}</p>
        @endif

        @forelse($favorites as $item)
        <div class="cart-item">
            <img src="{{ str_starts_with($item['image'] ?? '', 'http') ? $item['image'] : asset($item['image']) }}" alt="{{ $item['name'] }}">
            <div class="details">
                <div class="name">{{ $item['name'] }}</div>
                <div style="font-size:14px;color: var(--text-secondary);">{{ $item['brand'] }}</div>
                <div class="price">${{ number_format($item['price'], 0) }}</div>

                <div class="qty-remove">
                    <form action="{{ route('store.cart.add') }}" method="post" style="display:inline;">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $item['product_id'] }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="remove">Add to Cart</button>
                    </form>

                    <form action="{{ route('store.favorite.remove') }}" method="post" style="display:inline;">
                        @csrf
                        <input type="hidden" name="favorite_id" value="{{ $item['favorite_id'] }}">
                        <button type="submit" class="remove">Remove</button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <p style="color: var(--text-secondary); padding: 32px 0;">
            Your favorite list is empty. <a href="{{ url('/') }}">Continue shopping</a>.
        </p>
        @endforelse
    </div>
</div>
@endsection