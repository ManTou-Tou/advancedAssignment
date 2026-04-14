<?php

use App\Http\Controllers\StoreController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\RegisterController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrdersController;
use App\Http\Controllers\Admin\ProductsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// E-commerce store (main site) - requires login (user OR admin).
Route::middleware('auth.any')->group(function () {
    Route::get('/', [StoreController::class, 'home']);
    Route::get('/store', [StoreController::class, 'home']);
    Route::get('/store/product/{id}', [StoreController::class, 'product']);
    Route::get('/store/cart', [StoreController::class, 'cart'])->name('store.cart');
    Route::post('/store/cart/add', [StoreController::class, 'addToCart'])->name('store.cart.add');
    Route::post('/store/cart/remove', [StoreController::class, 'removeFromCart'])->name('store.cart.remove');
    Route::post('/store/cart/update', [StoreController::class, 'updateCartQuantity'])->name('store.cart.update');
    Route::get('/store/order/{id}/confirmation', [StoreController::class, 'orderConfirmation'])->name('store.order.confirmation');
    Route::get('/store/order/{id}/bill.pdf', [StoreController::class, 'billPdf'])->name('store.order.bill-pdf');

    // Favorites (session-based)
    Route::get('/favorite', [StoreController::class, 'favorite'])->name('store.favorite');
    Route::post('/favorite/add', [StoreController::class, 'addToFavorite'])->name('store.favorite.add');
    Route::post('/favorite/remove', [StoreController::class, 'removeFromFavorite'])->name('store.favorite.remove');

    // User-only checkout & order tracking
    Route::middleware('auth')->group(function () {
        Route::get('/store/checkout', [StoreController::class, 'checkout'])->name('store.checkout');
        Route::post('/store/order/place', [StoreController::class, 'placeOrder'])->name('store.order.place');
        Route::get('/order/{id}/pay', [StoreController::class, 'payOrder'])->name('store.order.pay');

        Route::get('/store/my-orders', [StoreController::class, 'myOrders'])->name('store.my-orders');
        Route::get('/store/my-orders/{id}', [StoreController::class, 'myOrderDetails'])->name('store.my-orders.details');
    });
});

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

// Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [LoginController::class, 'login']);

        // Admin self-registration is a security risk; keep it for local/dev only.
        if (app()->environment('local')) {
            Route::get('register', [RegisterController::class, 'showRegisterForm'])->name('register');
            Route::post('register', [RegisterController::class, 'register']);
        }
    });
    
    Route::middleware('auth:admin')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('orders', [OrdersController::class, 'index'])->name('orders');
        Route::post('orders/{orderId}/delivery', [OrdersController::class, 'updateDelivery'])->name('orders.delivery');
        Route::resource('products', ProductsController::class)->except(['show'])->names('products');
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    });
});   

require __DIR__.'/auth.php';
