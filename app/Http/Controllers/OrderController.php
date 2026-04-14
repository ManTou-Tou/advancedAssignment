<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function checkout(Request $request)
{
    $order = Order::create([
        'session_id' => session()->getId(),
        'subtotal' => $request->subtotal,
        'shipping' => $request->shipping,
        'total' => $request->total,
        'status' => 'pending'
    ]);

    return redirect('/order/' . $order->id);
}
}
