@extends('layouts.store')

@section('title', 'Checkout')

@section('content')
<div class="store-section" style="max-width: 720px; margin: 0 auto;">
    <h1 style="margin-bottom: 32px;">Checkout</h1>
    @if(session('error'))
        <p style="color: #c00; margin-bottom: 16px;">{{ session('error') }}</p>
    @endif

    <div style="background: var(--bg-light); border-radius: var(--radius); padding: 24px; margin-bottom: 32px;">
        <h3 style="margin-bottom: 16px;">Order Summary</h3>
        @foreach($cart as $item)
        <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--border);">
            <span>{{ $item['name'] }} × {{ $item['qty'] }}</span>
            <span>${{ number_format($item['price'] * $item['qty'], 2) }}</span>
        </div>
        @endforeach
        <div style="display: flex; justify-content: space-between; margin-top: 12px;">
            <span>Subtotal</span>
            <span>${{ number_format($subtotal, 2) }}</span>
        </div>
        <div style="display: flex; justify-content: space-between;">
            <span>Shipping</span>
            <span>{{ $shipping > 0 ? '$' . number_format($shipping, 2) : 'Free' }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 18px; margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--border);">
            <span>Total</span>
            <span>${{ number_format($total, 2) }}</span>
        </div>
    </div>

    <form action="{{ route('store.order.place') }}" method="post">
        @csrf
        <div style="background: var(--bg-light); border-radius: var(--radius); padding: 24px; margin-bottom: 24px;">
            <h3 style="margin-bottom: 16px;">Delivery details</h3>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:8px;">Full name</label>
                    <input type="text" name="delivery_name" value="{{ old('delivery_name', Auth::guard('web')->user()->name ?? '') }}" required
                        style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 8px; font-size: 16px;">
                    @error('delivery_name')<p style="color:#c00; font-size:14px; margin-top:6px;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:8px;">Phone number</label>
                    <input type="text" name="delivery_phone" value="{{ old('delivery_phone') }}" required
                        style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 8px; font-size: 16px;">
                    @error('delivery_phone')<p style="color:#c00; font-size:14px; margin-top:6px;">{{ $message }}</p>@enderror
                </div>
            </div>

            <div style="margin-top: 12px;">
                <label style="display:block; font-weight:600; margin-bottom:8px;">Address line 1</label>
                <input type="text" name="delivery_address_line1" value="{{ old('delivery_address_line1') }}" required
                    style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 8px; font-size: 16px;">
                @error('delivery_address_line1')<p style="color:#c00; font-size:14px; margin-top:6px;">{{ $message }}</p>@enderror
            </div>

            <div style="margin-top: 12px;">
                <label style="display:block; font-weight:600; margin-bottom:8px;">Address line 2 (optional)</label>
                <input type="text" name="delivery_address_line2" value="{{ old('delivery_address_line2') }}"
                    style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 8px; font-size: 16px;">
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 12px;">
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:8px;">City</label>
                    <input type="text" name="delivery_city" value="{{ old('delivery_city') }}" required
                        style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 8px; font-size: 16px;">
                    @error('delivery_city')<p style="color:#c00; font-size:14px; margin-top:6px;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:8px;">State</label>
                    <input type="text" name="delivery_state" value="{{ old('delivery_state') }}" required
                        style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 8px; font-size: 16px;">
                    @error('delivery_state')<p style="color:#c00; font-size:14px; margin-top:6px;">{{ $message }}</p>@enderror
                </div>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 12px;">
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:8px;">Postcode</label>
                    <input type="text" name="delivery_postcode" value="{{ old('delivery_postcode') }}" required
                        style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 8px; font-size: 16px;">
                    @error('delivery_postcode')<p style="color:#c00; font-size:14px; margin-top:6px;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="display:block; font-weight:600; margin-bottom:8px;">Country</label>
                    <input type="text" name="delivery_country" value="{{ old('delivery_country', 'Malaysia') }}" required
                        style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 8px; font-size: 16px;">
                    @error('delivery_country')<p style="color:#c00; font-size:14px; margin-top:6px;">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; font-weight: 600; margin-bottom: 12px;">Select payment method</label>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <label style="display: flex; align-items: center; gap: 12px; padding: 16px; border: 1px solid var(--border); border-radius: var(--radius); cursor: pointer;">
                    <input type="radio" name="payment_method" value="tng" required>
                    <span><strong>Touch 'n Go (TNG)</strong></span>
                </label>
                <label style="display: flex; align-items: center; gap: 12px; padding: 16px; border: 1px solid var(--border); border-radius: var(--radius); cursor: pointer;">
                    <input type="radio" name="payment_method" value="maybank">
                    <span><strong>Online Banking – Maybank</strong></span>
                </label>
                <label style="display: flex; align-items: center; gap: 12px; padding: 16px; border: 1px solid var(--border); border-radius: var(--radius); cursor: pointer;">
                    <input type="radio" name="payment_method" value="public_bank">
                    <span><strong>Online Banking – Public Bank</strong></span>
                </label>
            </div>
            @error('payment_method')
                <p style="color: #c00; font-size: 14px; margin-top: 8px;">{{ $message }}</p>
            @enderror
        </div>
        <div style="display: flex; gap: 12px;">
            <a href="{{ route('store.cart') }}" style="padding: 14px 24px; border: 1px solid var(--border); border-radius: var(--radius); color: var(--text);">Back to Cart</a>
            <button type="submit" class="btn-add-cart" style="padding: 14px 32px;">Place Order & Pay</button>
        </div>
    </form>
</div>
@endsection
