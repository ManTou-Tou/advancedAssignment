<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;
        
        foreach ($guards as $guard) {
            if ($guard === 'admin' && Auth::guard($guard)->check()) {
                if ($request->routeIs('admin.login') || $request->routeIs('admin.register')) {
                    return redirect('/admin/dashboard');
                }
            }
            
            if (($guard === 'web' || $guard === null) && Auth::guard('web')->check()) {
                if ($request->routeIs('login') || $request->routeIs('register')) {
                    return redirect('/store');
                }
            }
        }
        
        return $next($request);
    }
}
