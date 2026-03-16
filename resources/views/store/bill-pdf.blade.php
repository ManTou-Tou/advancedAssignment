<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bill – Order #{{ $order['id'] }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; margin: 24px; }
        h1 { font-size: 20px; margin-bottom: 8px; }
        .meta { color: #666; margin-bottom: 24px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        th, td { border: 1px solid #ddd; padding: 8px 12px; text-align: left; }
        th { background: #f5f5f5; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; font-size: 14px; }
        .payment { margin-top: 24px; padding-top: 16px; border-top: 1px solid #ddd; }
    </style>
</head>
<body>
    <h1>Invoice / Bill</h1>
    <div class="meta">Order #{{ $order['id'] }} · {{ $order['created_at'] ?? now()->toDateTimeString() }}</div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order['items'] as $item)
            <tr>
                <td>{{ $item['product_name'] }}</td>
                <td class="text-right">{{ $item['quantity'] }}</td>
                <td class="text-right">${{ number_format($item['price'], 2) }}</td>
                <td class="text-right">${{ number_format($item['price'] * $item['quantity'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table style="width: 280px; margin-left: auto;">
        <tr>
            <td>Subtotal</td>
            <td class="text-right">${{ number_format($order['subtotal'], 2) }}</td>
        </tr>
        <tr>
            <td>Shipping</td>
            <td class="text-right">${{ number_format($order['shipping'], 2) }}</td>
        </tr>
        <tr class="total-row">
            <td>Total</td>
            <td class="text-right">${{ number_format($order['total'], 2) }}</td>
        </tr>
    </table>

    <div class="payment">
        <p><strong>Payment method:</strong> {{ $order['payment_method'] }}</p>
        <p><strong>Reference:</strong> {{ $order['payment_reference'] }}</p>
    </div>

    <p style="margin-top: 32px; color: #666; font-size: 10px;">Thank you for shopping with us. This is a computer-generated bill.</p>
</body>
</html>
