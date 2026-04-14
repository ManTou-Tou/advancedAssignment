<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrdersController extends Controller
{
    public function index(): View
    {
        $orders = DB::table('orders')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->select(
                'orders.*',
                'users.name as user_name',
                'users.email as user_email',
            )
            ->orderByDesc('orders.id')
            ->limit(50)
            ->get();

        return view('admin.orders', ['orders' => $orders]);
    }

    public function updateDelivery(Request $request, int $orderId): RedirectResponse
    {
        $data = $request->validate([
            'delivery_status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'tracking_number' => 'nullable|string|max:100',
        ]);

        $update = [
            'delivery_status' => $data['delivery_status'],
            'tracking_number' => $data['tracking_number'] ?? null,
            'updated_at' => now(),
        ];

        if ($data['delivery_status'] === 'shipped') {
            $update['shipped_at'] = now();
        }
        if ($data['delivery_status'] === 'delivered') {
            $update['delivered_at'] = now();
        }

        DB::table('orders')->where('id', $orderId)->update($update);

        return back()->with('success', 'Delivery updated.');
    }
}

