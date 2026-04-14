<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Orders</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: #f3f4f6;
            padding: 28px;
        }
        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 22px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .topbar {
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:12px;
            flex-wrap:wrap;
            margin-bottom: 14px;
        }
        h1 { font-size: 28px; margin: 0; color:#111827; }
        .muted { color:#6b7280; font-size: 12px; }
        a.btn {
            display:inline-block;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            text-decoration:none;
            color:#111827;
            background:#fff;
            font-weight: 700;
            font-size: 13px;
        }
        table { width:100%; border-collapse: collapse; }
        th, td {
            padding: 10px 8px;
            border-bottom: 1px solid #eef2f7;
            font-size: 13px;
            vertical-align: top;
            text-align:left;
        }
        th {
            position: sticky;
            top: 0;
            background:#f9fafb;
            z-index:1;
            font-weight: 800;
            color:#374151;
        }
        .pill {
            display:inline-block;
            padding: 3px 8px;
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            background:#f9fafb;
            font-size: 12px;
            font-weight: 800;
            color:#111827;
        }
        select, input {
            padding: 8px 10px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            font-size: 13px;
        }
        .save-btn {
            padding: 8px 12px;
            border: 0;
            border-radius: 10px;
            background:#2563eb;
            color:#fff;
            font-weight: 800;
            cursor: pointer;
        }
        .save-btn:hover { background:#1d4ed8; }
        .success {
            background: #d1fae5;
            border: 1px solid #a7f3d0;
            color:#065f46;
            padding: 10px 12px;
            border-radius: 12px;
            margin-bottom: 12px;
            font-size: 13px;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="topbar">
            <div>
                <h1>Orders & Delivery</h1>
                <div class="muted">Shows latest 50 orders. Users are linked when the buyer was logged in.</div>
            </div>
            <a class="btn" href="{{ route('admin.dashboard') }}">Back to Dashboard</a>
        </div>

        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        <div style="overflow:auto; border:1px solid #eef2f7; border-radius: 14px;">
            <table>
                <thead>
                    <tr>
                        <th style="width:70px;">Order</th>
                        <th style="min-width:220px;">Buyer</th>
                        <th style="min-width:260px;">Delivery address</th>
                        <th style="width:140px;">Total</th>
                        <th style="width:140px;">Order status</th>
                        <th style="min-width:300px;">Delivery setup</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $o)
                        <tr>
                            <td>
                                <div style="font-weight:900;">#{{ $o->id }}</div>
                                <div class="muted">{{ \Illuminate\Support\Carbon::parse($o->created_at)->format('Y-m-d H:i') }}</div>
                            </td>
                            <td>
                                <div style="font-weight:800;">
                                    {{ $o->user_name ?: ($o->delivery_name ?: 'Guest') }}
                                </div>
                                <div class="muted">{{ $o->user_email ?: '—' }}</div>
                                <div class="muted">session: {{ $o->session_id }}</div>
                            </td>
                            <td>
                                <div style="font-weight:800;">{{ $o->delivery_name ?: '—' }} · {{ $o->delivery_phone ?: '—' }}</div>
                                <div class="muted">
                                    {{ $o->delivery_address_line1 ?: '—' }}
                                    @if($o->delivery_address_line2) , {{ $o->delivery_address_line2 }} @endif
                                    <br>
                                    {{ $o->delivery_postcode ?: '—' }} {{ $o->delivery_city ?: '' }}, {{ $o->delivery_state ?: '' }}
                                    <br>
                                    {{ $o->delivery_country ?: '' }}
                                </div>
                            </td>
                            <td>
                                <div style="font-weight:900;">RM {{ number_format((float)$o->total, 2) }}</div>
                                <div class="muted">ship RM {{ number_format((float)$o->shipping, 2) }}</div>
                            </td>
                            <td>
                                <span class="pill">{{ $o->status }}</span>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.orders.delivery', ['orderId' => $o->id]) }}" style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
                                    @csrf
                                    <select name="delivery_status" required>
                                        @foreach(['pending','processing','shipped','delivered','cancelled'] as $st)
                                            <option value="{{ $st }}" @selected(($o->delivery_status ?? 'pending') === $st)>{{ ucfirst($st) }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="tracking_number" placeholder="Tracking #" value="{{ $o->tracking_number ?? '' }}">
                                    <button type="submit" class="save-btn">Save</button>
                                </form>
                                <div class="muted" style="margin-top:6px;">
                                    shipped: {{ $o->shipped_at ? \Illuminate\Support\Carbon::parse($o->shipped_at)->format('Y-m-d H:i') : '—' }}
                                    · delivered: {{ $o->delivered_at ? \Illuminate\Support\Carbon::parse($o->delivered_at)->format('Y-m-d H:i') : '—' }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="muted" style="padding:14px;">No orders yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

