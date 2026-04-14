<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Products</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, sans-serif; background:#f3f4f6; padding:28px; }
        .card { background:#fff; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.08); padding:22px; max-width:1200px; margin:0 auto; }
        .topbar { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:14px; }
        h1 { font-size:28px; margin:0; color:#111827; }
        .muted { color:#6b7280; font-size:12px; }
        a.btn { display:inline-block; padding:10px 14px; border-radius:10px; border:1px solid #e5e7eb; text-decoration:none; color:#111827; background:#fff; font-weight:800; font-size:13px; }
        a.primary { background:#2563eb; border-color:#2563eb; color:#fff; }
        a.primary:hover { background:#1d4ed8; border-color:#1d4ed8; }
        table { width:100%; border-collapse:collapse; min-width: 900px; }
        th, td { padding:10px 8px; border-bottom:1px solid #eef2f7; font-size:13px; vertical-align:top; text-align:left; }
        th { position:sticky; top:0; background:#f9fafb; z-index:1; font-weight:900; color:#374151; }
        .success { background:#d1fae5; border:1px solid #a7f3d0; color:#065f46; padding:10px 12px; border-radius:12px; margin-bottom:12px; font-size:13px; font-weight:800; }
        .actions { display:flex; gap:8px; align-items:center; }
        .danger { background:#ef4444; border-color:#ef4444; color:#fff; }
        .danger:hover { background:#dc2626; border-color:#dc2626; }
        form { display:inline; }
        .pager { margin-top: 14px; font-size: 13px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="topbar">
            <div>
                <h1>Products</h1>
                <div class="muted">Add/edit/delete products stored in `products` table.</div>
            </div>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <a class="btn" href="{{ route('admin.dashboard') }}">Back to Dashboard</a>
                <a class="btn primary" href="{{ route('admin.products.create') }}">+ Add Product</a>
            </div>
        </div>

        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        <div style="overflow:auto; border:1px solid #eef2f7; border-radius: 14px;">
            <table>
                <thead>
                    <tr>
                        <th style="width:70px;">ID</th>
                        <th style="min-width:260px;">Name</th>
                        <th style="width:140px;">Brand</th>
                        <th style="width:120px;">Category</th>
                        <th style="width:120px;">Price</th>
                        <th style="width:120px;">Rating</th>
                        <th style="width:110px;">Stock</th>
                        <th style="width:220px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $p)
                        <tr>
                            <td>#{{ $p->id }}</td>
                            <td>
                                <div style="font-weight:900; color:#111827;">{{ $p->name }}</div>
                                <div class="muted" style="margin-top:4px; word-break: break-all;">{{ $p->image }}</div>
                            </td>
                            <td>{{ $p->brand }}</td>
                            <td>{{ $p->category }}</td>
                            <td>RM {{ number_format((float)$p->price, 2) }}</td>
                            <td>{{ number_format((float)$p->rating, 1) }}</td>
                            <td>{{ (int)$p->stock }}</td>
                            <td>
                                <div class="actions">
                                    <a class="btn" href="{{ route('admin.products.edit', $p) }}">Edit</a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $p) }}" onsubmit="return confirm('Delete this product?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="muted" style="padding:14px;">No products yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pager">
            {{ $products->links() }}
        </div>
    </div>
</body>
</html>

