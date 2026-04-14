@extends('layouts.store')

@section('title', 'My Orders')

@section('content')
<div class="store-section" style="max-width: 920px; margin: 0 auto;">
    <h1 style="margin-bottom: 18px;">My Orders</h1>
    <p style="color: var(--text-secondary); margin-bottom: 22px;">Track your delivery status and view what you bought.</p>

    <div style="background: #fff; border: 1px solid var(--border); border-radius: var(--radius); overflow: auto;">
        <table style="width:100%; border-collapse: collapse; min-width: 700px;">
            <thead>
                <tr style="background: var(--bg-light);">
                    <th style="text-align:left; padding: 12px 14px; border-bottom:1px solid var(--border);">Order</th>
                    <th style="text-align:left; padding: 12px 14px; border-bottom:1px solid var(--border);">Total</th>
                    <th style="text-align:left; padding: 12px 14px; border-bottom:1px solid var(--border);">Delivery status</th>
                    <th style="text-align:left; padding: 12px 14px; border-bottom:1px solid var(--border);">Tracking</th>
                    <th style="text-align:left; padding: 12px 14px; border-bottom:1px solid var(--border);">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $o)
                    <tr>
                        <td style="padding: 12px 14px; border-bottom:1px solid var(--border);">
                            <div style="font-weight: 800;">#{{ $o['id'] }}</div>
                            <div style="font-size: 13px; color: var(--text-secondary);">{{ \Illuminate\Support\Carbon::parse($o['created_at'])->format('Y-m-d H:i') }}</div>
                        </td>
                        <td style="padding: 12px 14px; border-bottom:1px solid var(--border); font-weight: 800;">
                            RM {{ number_format((float)$o['total'], 2) }}
                        </td>
                        <td style="padding: 12px 14px; border-bottom:1px solid var(--border);">
                            <span style="display:inline-block; padding: 4px 10px; border-radius: 999px; border: 1px solid var(--border); background: var(--bg-light); font-weight: 800; font-size: 12px;">
                                {{ ucfirst($o['delivery_status'] ?? 'pending') }}
                            </span>
                        </td>
                        <td style="padding: 12px 14px; border-bottom:1px solid var(--border);">
                            {{ $o['tracking_number'] ?? '—' }}
                        </td>
                        <td style="padding: 12px 14px; border-bottom:1px solid var(--border);">
                            <a href="{{ route('store.my-orders.details', ['id' => $o['id']]) }}" class="btn-add-cart" style="padding: 10px 14px; display:inline-block;">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding: 14px; color: var(--text-secondary);">No orders yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

