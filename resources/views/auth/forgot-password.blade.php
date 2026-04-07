@extends('layouts.store')

@section('title', 'Forgot Password')

@section('content')
<div class="auth-container" style="min-height: 60vh; display: flex; align-items: center; justify-content: center; padding: 60px 20px;">
    <div class="auth-card" style="max-width: 450px; width: 100%; background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); padding: 40px;">
        <h2 style="text-align: center; margin-bottom: 8px;">Reset Password</h2>
        <p style="text-align: center; color: var(--text-secondary); margin-bottom: 32px;">
            Enter your email address and we'll send you a link to reset your password.
        </p>

        @if(session('status'))
            <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div style="margin-bottom: 24px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    style="width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 8px; font-size: 16px;">
            </div>

            <button type="submit" style="width: 100%; padding: 14px; background: var(--accent); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 16px; cursor: pointer;">
                Send Password Reset Link
            </button>
        </form>

        <p style="text-align: center; margin-top: 24px; color: var(--text-secondary);">
            <a href="{{ route('login') }}" style="color: var(--accent);">Back to Login</a>
        </p>
    </div>
</div>
@endsection
