@extends('layouts.store')

@section('title', 'Order Details')

@section('content')
<div class="store-section" style="max-width: 820px; margin: 0 auto;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap: 12px; flex-wrap:wrap; margin-bottom: 18px;">
        <div>
            <h1 style="margin-bottom: 6px;">Order #{{ $order['id'] }}</h1>
            <p style="color: var(--text-secondary);">Placed at {{ \Illuminate\Support\Carbon::parse($order['created_at'])->format('Y-m-d H:i') }}</p>
        </div>
        <a href="{{ route('store.my-orders') }}" style="padding: 12px 16px; border: 1px solid var(--border); border-radius: var(--radius); color: var(--text);">Back</a>
    </div>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 14px;">
        <div style="background: var(--bg-light); border-radius: var(--radius); padding: 18px;">
            <h3 style="margin-bottom: 10px;">Delivery status</h3>
            <div style="font-weight: 900; font-size: 18px; margin-bottom: 8px;">
                {{ ucfirst($order['delivery_status'] ?? 'pending') }}
            </div>
            <div style="color: var(--text-secondary); font-size: 14px;">
                Tracking: <b>{{ $order['tracking_number'] ?? '—' }}</b>
            </div>
            <div style="color: var(--text-secondary); font-size: 14px; margin-top: 8px;">
                Shipped: {{ $order['shipped_at'] ? \Illuminate\Support\Carbon::parse($order['shipped_at'])->format('Y-m-d H:i') : '—' }}<br>
                Delivered: {{ $order['delivered_at'] ? \Illuminate\Support\Carbon::parse($order['delivered_at'])->format('Y-m-d H:i') : '—' }}
            </div>
        </div>

        <div style="background: var(--bg-light); border-radius: var(--radius); padding: 18px;">
            <h3 style="margin-bottom: 10px;">Delivery address</h3>
            <div style="font-weight: 800;">{{ $order['delivery_name'] ?? '—' }} · {{ $order['delivery_phone'] ?? '—' }}</div>
            <div style="color: var(--text-secondary); font-size: 14px; margin-top: 8px;">
                {{ $order['delivery_address_line1'] ?? '—' }}<br>
                @if(!empty($order['delivery_address_line2']))
                    {{ $order['delivery_address_line2'] }}<br>
                @endif
                {{ $order['delivery_postcode'] ?? '—' }} {{ $order['delivery_city'] ?? '' }}, {{ $order['delivery_state'] ?? '' }}<br>
                {{ $order['delivery_country'] ?? '' }}
            </div>
        </div>
    </div>

    <div style="background: #fff; border: 1px solid var(--border); border-radius: var(--radius); padding: 18px; margin-top: 14px;">
        <h3 style="margin-bottom: 12px;">Items</h3>
        @foreach($order['items'] as $item)
            <div style="display:flex; justify-content:space-between; gap: 12px; padding: 10px 0; border-bottom: 1px solid var(--border);">
                <div>
                    <div style="font-weight: 800;">{{ $item['product_name'] }}</div>
                    <div style="color: var(--text-secondary); font-size: 14px;">Qty: {{ $item['quantity'] }}</div>
                </div>
                <div style="font-weight: 900;">
                    RM {{ number_format((float)$item['price'] * (int)$item['quantity'], 2) }}
                </div>
            </div>
        @endforeach
        <div style="display:flex; justify-content:space-between; padding-top: 12px; margin-top: 12px; border-top: 1px solid var(--border); font-weight: 900;">
            <span>Total</span>
            <span>RM {{ number_format((float)$order['total'], 2) }}</span>
        </div>
    </div>
</div>
@endsection

