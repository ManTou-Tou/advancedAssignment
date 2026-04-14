<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: #f3f4f6;
            min-height: 100vh;
            padding: 28px;
        }
        
        .dashboard-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 28px;
            max-width: 1100px;
            margin: 0 auto;
        }
        
        h1 {
            font-size: 32px;
            margin-bottom: 16px;
            color: #222;
        }
        
        .topbar {
            display: flex;
            gap: 16px;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .welcome-text {
            font-size: 18px;
            color: #222;
            margin-bottom: 8px;
        }
        
        .status-text {
            font-size: 16px;
            color: #666;
            margin-bottom: 32px;
        }

        .grid {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 18px;
        }

        @media (max-width: 920px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }

        .panel {
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 16px;
            background: #fff;
        }

        .panel h2 {
            font-size: 18px;
            color: #111827;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px 8px;
            border-bottom: 1px solid #eef2f7;
            font-size: 14px;
            text-align: left;
            vertical-align: top;
        }

        th {
            color: #374151;
            font-weight: 700;
            background: #f9fafb;
            position: sticky;
            top: 0;
        }

        .muted {
            color: #6b7280;
            font-size: 12px;
        }

        .kpi {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 14px;
        }

        .kpi-card {
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 12px;
            background: #f9fafb;
        }

        .kpi-label {
            font-size: 12px;
            color: #6b7280;
        }

        .kpi-value {
            margin-top: 6px;
            font-size: 20px;
            font-weight: 800;
            color: #111827;
        }

        .chart-wrap {
            height: 320px;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
        }

        @media (max-width: 920px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
        }

        .chart-card {
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 14px;
            background: #fff;
        }

        .chart-title {
            font-size: 14px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 8px;
        }

        .chart-sub {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 10px;
        }

        .pie-wrap {
            height: 240px;
        }
        
        .logout-btn {
            background: #dc2626;
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }
        
        .logout-btn:hover {
            background: #b91c1c;
        }
    </style>
</head>
<body>
    <div class="dashboard-card">
        <div class="topbar">
            <div>
                <h1>Admin Dashboard</h1>
                <p class="welcome-text">Welcome back, {{ Auth::guard('admin')->user()->name }}!</p>
                <p class="muted">Sales are calculated from orders with payment status: <b>successful</b>.</p>
            </div>
            <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                <a href="{{ route('admin.products.index') }}" style="display:inline-block; padding:10px 14px; border-radius:10px; border:1px solid #e5e7eb; text-decoration:none; font-weight:800; font-size:13px; color:#111827; background:#fff;">Manage Products</a>
                <a href="{{ route('admin.orders') }}" style="display:inline-block; padding:10px 14px; border-radius:10px; border:1px solid #e5e7eb; text-decoration:none; font-weight:800; font-size:13px; color:#111827; background:#fff;">Manage Orders</a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            </div>
        </div>

        @php
            $totalUnits = (int) collect($topProducts ?? [])->sum('units_sold');
            $totalRevenue = (float) collect($topProducts ?? [])->sum('revenue');
            $topName = (string) (collect($topProducts ?? [])->first()->product_name ?? '—');
        @endphp

        <div class="kpi">
            <div class="kpi-card">
                <div class="kpi-label">Top products shown</div>
                <div class="kpi-value">{{ count($topProducts ?? []) }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Total units (top list)</div>
                <div class="kpi-value">{{ number_format($totalUnits) }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Total revenue (top list)</div>
                <div class="kpi-value">RM {{ number_format($totalRevenue, 2) }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">#1 product</div>
                <div class="kpi-value" style="font-size:16px; line-height:1.2;">{{ $topName }}</div>
            </div>
        </div>

        <div class="grid">
            <div class="panel">
                <h2>Top selling products (by units sold)</h2>
                <div style="max-height: 420px; overflow:auto; border-radius: 12px; border:1px solid #eef2f7;">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 44px;">#</th>
                                <th>Product</th>
                                <th style="width: 120px;">Units</th>
                                <th style="width: 160px;">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($topProducts ?? []) as $i => $p)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>
                                        <div style="font-weight:700; color:#111827;">{{ $p->product_name }}</div>
                                        <div class="muted">
                                            {{ $p->brand ?: '—' }}
                                            @if($p->category)
                                                · {{ $p->category }}
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ number_format((int) $p->units_sold) }}</td>
                                    <td>RM {{ number_format((float) $p->revenue, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="muted" style="padding: 14px;">
                                        No sales yet. Place an order (payment status successful) and refresh.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="panel">
                <h2>Hot sales distribution</h2>
                <div class="charts-grid">
                    <div class="chart-card">
                        <div class="chart-title">Top products (units)</div>
                        <div class="chart-sub">Share of units sold by product (top 8)</div>
                        <div class="pie-wrap"><canvas id="pieProducts"></canvas></div>
                    </div>
                    <div class="chart-card">
                        <div class="chart-title">Category split</div>
                        <div class="chart-sub">Phones vs laptops vs other</div>
                        <div class="pie-wrap"><canvas id="pieCategories"></canvas></div>
                    </div>
                    <div class="chart-card">
                        <div class="chart-title">Brand distribution</div>
                        <div class="chart-sub">Units sold by brand (top 8)</div>
                        <div class="pie-wrap"><canvas id="pieBrands"></canvas></div>
                    </div>
                </div>
                <p class="muted" style="margin-top:10px;">
                    Data is calculated from successful payments only.
                </p>
            </div>
        </div>

        <div class="panel" style="margin-top: 18px;">
            <div style="display:flex; justify-content:space-between; gap:12px; align-items:center; flex-wrap:wrap;">
                <h2 style="margin:0;">Recent orders</h2>
                <a href="{{ route('admin.orders') }}" style="font-size: 13px; font-weight: 800; color: #2563eb; text-decoration:none;">Manage delivery →</a>
            </div>
            <div class="muted" style="margin: 6px 0 12px;">Buyer appears when user was logged in at checkout.</div>
            <div style="overflow:auto; border:1px solid #eef2f7; border-radius: 14px;">
                <table>
                    <thead>
                        <tr>
                            <th style="width:80px;">Order</th>
                            <th>Buyer</th>
                            <th style="width:160px;">Total</th>
                            <th style="width:160px;">Delivery</th>
                            <th style="width:220px;">Tracking</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($recentOrders ?? []) as $o)
                            <tr>
                                <td>#{{ $o->id }}</td>
                                <td>
                                    <div style="font-weight:800; color:#111827;">{{ $o->user_name ?: 'Guest' }}</div>
                                    <div class="muted">{{ $o->user_email ?: '—' }}</div>
                                </td>
                                <td>RM {{ number_format((float)$o->total, 2) }}</td>
                                <td><span style="display:inline-block; padding: 3px 8px; border-radius:999px; border:1px solid #e5e7eb; background:#f9fafb; font-size: 12px; font-weight: 900;">{{ ucfirst($o->delivery_status ?? 'pending') }}</span></td>
                                <td class="muted">{{ $o->tracking_number ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="muted" style="padding:14px;">No orders yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const productLabels = @json($productPieLabels ?? []);
        const productUnits = @json($productPieUnits ?? []);
        const categoryLabels = @json($categoryLabels ?? []);
        const categoryUnits = @json($categoryUnits ?? []);
        const brandLabels = @json($brandLabels ?? []);
        const brandUnits = @json($brandUnits ?? []);

        const palette = [
            '#2563eb', '#16a34a', '#f97316', '#a855f7', '#ef4444', '#0ea5e9',
            '#f59e0b', '#10b981', '#64748b', '#db2777', '#84cc16', '#8b5cf6'
        ];

        function makeDoughnut(el, labels, data) {
            if (!el) return;
            const colors = labels.map((_, i) => palette[i % palette.length]);
            new Chart(el, {
                type: 'doughnut',
                data: {
                    labels,
                    datasets: [{
                        data,
                        backgroundColor: colors.map(c => c + '33'),
                        borderColor: colors,
                        borderWidth: 2,
                        hoverOffset: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: { callbacks: {
                            label: (ctx) => {
                                const v = ctx.parsed;
                                const total = (ctx.dataset.data || []).reduce((a,b) => a + (Number(b)||0), 0);
                                const pct = total > 0 ? ((v / total) * 100).toFixed(1) : '0.0';
                                return ` ${ctx.label}: ${v} (${pct}%)`;
                            }
                        }}
                    },
                    cutout: '62%',
                }
            });
        }

        makeDoughnut(document.getElementById('pieProducts'), productLabels, productUnits);
        makeDoughnut(document.getElementById('pieCategories'), categoryLabels, categoryUnits);
        makeDoughnut(document.getElementById('pieBrands'), brandLabels, brandUnits);
    </script>
</body>
</html>