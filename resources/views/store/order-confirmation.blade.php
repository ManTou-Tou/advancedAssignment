@extends('layouts.store')

@section('title', 'Order Confirmation')

@section('content')
<div class="store-section" style="max-width: 640px; margin: 0 auto; text-align: center;">
    <h1 style="margin-bottom: 16px;">Thank you for your order</h1>
    <p style="color: var(--text-secondary); margin-bottom: 32px;">Order #{{ $order['id'] }} has been placed successfully.</p>

    <div style="background: var(--bg-light); border-radius: var(--radius); padding: 24px; margin-bottom: 32px; text-align: left;">
        <div style="margin-bottom: 12px;"><strong>Payment method:</strong> {{ $order['payment_method'] }}</div>
        <div style="margin-bottom: 12px;"><strong>Reference:</strong> {{ $order['payment_reference'] }}</div>
        <div style="margin-bottom: 12px;"><strong>Total paid:</strong> ${{ number_format($order['total'], 2) }}</div>
        <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border);">
            @foreach($order['items'] as $item)
            <div style="display: flex; justify-content: space-between; padding: 6px 0;">{{ $item['product_name'] }} × {{ $item['quantity'] }} — ${{ number_format($item['price'] * $item['quantity'], 2) }}</div>
            @endforeach
        </div>
    </div>

    <p style="font-weight: 600; margin-bottom: 16px;">Do you want to print the bill (PDF)?</p>
    <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
        <a href="{{ route('store.order.bill-pdf', ['id' => $order['id']]) }}" class="btn-primary" style="display: inline-block;">Yes, download PDF bill</a>
        <a href="{{ url('/') }}" style="display: inline-block; padding: 14px 24px; border: 1px solid var(--border); border-radius: var(--radius); color: var(--text);">No, continue shopping</a>
    </div>
</div>
@endsection
