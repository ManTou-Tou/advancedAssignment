<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $salesBase = fn () => DB::table('order_items')
            ->join('payments', 'order_items.order_id', '=', 'payments.order_id')
            ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
            ->where('payments.status', 'successful');

        $topProducts = $salesBase()
            ->groupBy('order_items.product_id', 'order_items.product_name', 'products.brand', 'products.category')
            ->selectRaw('
                order_items.product_id,
                order_items.product_name,
                COALESCE(products.brand, "") as brand,
                COALESCE(products.category, "") as category,
                SUM(order_items.quantity) as units_sold,
                SUM(order_items.price * order_items.quantity) as revenue
            ')
            ->orderByDesc('units_sold')
            ->limit(8)
            ->get();

        // Pie/Doughnut datasets
        $productPieLabels = $topProducts->pluck('product_name')->all();
        $productPieUnits = $topProducts->pluck('units_sold')->map(fn ($v) => (int) $v)->all();

        $categoryRows = $salesBase()
            ->groupBy('products.category')
            ->selectRaw('COALESCE(products.category, "other") as category, SUM(order_items.quantity) as units_sold')
            ->orderByDesc('units_sold')
            ->get();

        $categoryLabels = $categoryRows->pluck('category')->map(fn ($c) => $c === '' ? 'other' : $c)->all();
        $categoryUnits = $categoryRows->pluck('units_sold')->map(fn ($v) => (int) $v)->all();

        $brandRows = $salesBase()
            ->groupBy('products.brand')
            ->selectRaw('COALESCE(products.brand, "unknown") as brand, SUM(order_items.quantity) as units_sold')
            ->orderByDesc('units_sold')
            ->limit(8)
            ->get();

        $brandLabels = $brandRows->pluck('brand')->map(fn ($b) => $b === '' ? 'unknown' : $b)->all();
        $brandUnits = $brandRows->pluck('units_sold')->map(fn ($v) => (int) $v)->all();

        $recentOrders = DB::table('orders')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->select(
                'orders.id',
                'orders.total',
                'orders.delivery_status',
                'orders.tracking_number',
                'orders.created_at',
                'users.name as user_name',
                'users.email as user_email',
            )
            ->orderByDesc('orders.id')
            ->limit(8)
            ->get();

        return view('admin.dashboard', [
            'topProducts' => $topProducts,
            'productPieLabels' => $productPieLabels,
            'productPieUnits' => $productPieUnits,
            'categoryLabels' => $categoryLabels,
            'categoryUnits' => $categoryUnits,
            'brandLabels' => $brandLabels,
            'brandUnits' => $brandUnits,
            'recentOrders' => $recentOrders,
        ]);
    }
}

