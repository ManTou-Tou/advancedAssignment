<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Add Product</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, sans-serif; background:#f3f4f6; padding:28px; }
        .card { background:#fff; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.08); padding:22px; max-width:900px; margin:0 auto; }
        .topbar { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:14px; }
        h1 { font-size:26px; margin:0; color:#111827; }
        a.btn { display:inline-block; padding:10px 14px; border-radius:10px; border:1px solid #e5e7eb; text-decoration:none; color:#111827; background:#fff; font-weight:800; font-size:13px; }
        .primary { background:#2563eb; border:0; color:#fff; padding:12px 16px; border-radius:12px; font-weight:900; cursor:pointer; }
        .primary:hover { background:#1d4ed8; }
    </style>
</head>
<body>
    <div class="card">
        <div class="topbar">
            <h1>Add Product</h1>
            <a class="btn" href="{{ route('admin.products.index') }}">Back</a>
        </div>

        <form method="POST" action="{{ route('admin.products.store') }}">
            @csrf
            @include('admin.products._form')

            <div style="margin-top:16px; display:flex; justify-content:flex-end;">
                <button type="submit" class="primary">Create</button>
            </div>
        </form>
    </div>
</body>
</html>

