<?php

use App\Http\Controllers\StoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// E-commerce store (main site)
Route::get('/', [StoreController::class, 'home']);
Route::get('/store', [StoreController::class, 'home']);
Route::get('/store/product/{id}', [StoreController::class, 'product']);
Route::get('/store/cart', [StoreController::class, 'cart'])->name('store.cart');
Route::post('/store/cart/add', [StoreController::class, 'addToCart'])->name('store.cart.add');
Route::post('/store/cart/remove', [StoreController::class, 'removeFromCart'])->name('store.cart.remove');
Route::post('/store/cart/update', [StoreController::class, 'updateCartQuantity'])->name('store.cart.update');
Route::get('/store/checkout', [StoreController::class, 'checkout'])->name('store.checkout');
Route::post('/store/order/place', [StoreController::class, 'placeOrder'])->name('store.order.place');
Route::get('/store/order/{id}/confirmation', [StoreController::class, 'orderConfirmation'])->name('store.order.confirmation');
Route::get('/store/order/{id}/bill.pdf', [StoreController::class, 'billPdf'])->name('store.order.bill-pdf');

// Legacy: users table view & db-test
Route::get('/users', function () {
    try {
        $users = DB::table('users')->orderBy('id')->get();
        return view('users', ['users' => $users]);
    } catch (\Throwable $e) {
        return view('users', ['users' => collect(), 'error' => $e->getMessage()]);
    }
});

Route::get('/db-test', function (Request $request) {
    try {
        // Values Laravel is using (from .env via config)
        $config = config('database.connections.mysql');
        $connected = DB::select('SELECT 1 as ok')[0]->ok ?? 0;
        $tables = DB::select('SHOW TABLES');
        $tableList = array_map(fn ($row) => array_values((array) $row)[0], $tables);
        $requestedTable = (string) $request->query('table', 'users');
        $limit = max(1, min((int) $request->query('limit', 50), 200));

        if (!in_array($requestedTable, $tableList, true)) {
            return response()->json([
                'connected_via_env' => true,
                'error' => true,
                'message' => "Table '{$requestedTable}' not found in database.",
                'available_tables' => $tableList,
            ], 404, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        $rows = DB::table($requestedTable)->limit($limit)->get();

        return response()->json([
            'connected_via_env' => true,
            'connection' => [
                'driver' => $config['driver'],
                'host' => $config['host'],
                'port' => $config['port'],
                'database' => $config['database'],
                'username' => $config['username'],
            ],
            'ping' => $connected === 1 ? 'OK' : 'fail',
            'selected_table' => $requestedTable,
            'rows_returned' => $rows->count(),
            'rows' => $rows,
            'available_tables' => $tableList,
        ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    } catch (\Throwable $e) {
        return response()->json([
            'connected_via_env' => false,
            'error' => true,
            'message' => $e->getMessage(),
        ], 500, [], JSON_PRETTY_PRINT);
    }
});

Route::get('/no-db-test', function () {
    return response()->json(['ok' => true]);
});

