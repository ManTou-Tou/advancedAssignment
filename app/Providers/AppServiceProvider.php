<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // MySQL utf8mb4: unique indexes on VARCHAR(255) exceed max key length (767/1000 bytes).
        // Use 191 chars for default string columns so migrations like users.email unique() succeed.
        Schema::defaultStringLength(191);

        View::composer('layouts.store', function ($view) {
            $sessionId = session()->get('_cart_token') ?? session()->getId();
            $cartItemCount = (int) DB::table('cart_items')->where('session_id', $sessionId)->sum('quantity');
            $view->with('cartItemCount', $cartItemCount);
        });
    }
}
