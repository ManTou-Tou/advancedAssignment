@extends('layouts.store')

@section('title', 'Admin Login')

@section('content')
<div class="auth-container" style="min-height: 60vh; display: flex; align-items: center; justify-content: center; padding: 60px 20px;">
    <div class="auth-card" style="max-width: 450px; width: 100%; background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); padding: 40px;">
        <h2 style="text-align: center; margin-bottom: 8px;">Admin Login</h2>
        <p style="text-align: center; color: var(--text-secondary); margin-bottom: 32px;">Sign in to admin panel</p>

        @if(session('success'))
            <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 8px; font-size: 16px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Password</label>
                <input type="password" name="password" required
                    style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 8px; font-size: 16px;">
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <label style="display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" name="remember">
                    <span style="font-size: 14px;">Remember me</span>
                </label>
            </div>

            <button type="submit" style="width: 100%; padding: 14px; background: var(--accent); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 16px; cursor: pointer;">
                Login as Admin
            </button>
        </form>

        <p style="text-align: center; margin-top: 24px; color: var(--text-secondary);">
            @if (\Illuminate\Support\Facades\Route::has('admin.register'))
                <a href="{{ route('admin.register') }}" style="color: var(--accent);">Register new admin</a>
            @endif
        </p>
    </div>
</div>
@endsection