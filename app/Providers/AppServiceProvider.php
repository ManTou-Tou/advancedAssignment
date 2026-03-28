<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
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
        View::composer('layouts.store', function ($view) {
            $sessionId = session()->get('_cart_token') ?? session()->getId();
            $cartItemCount = (int) DB::table('cart_items')->where('session_id', $sessionId)->sum('quantity');
            $view->with('cartItemCount', $cartItemCount);
        });
    }
}
