@extends('auth.layouts.app')

@section('content')
<div class="brand-wrapper">
    <img src="{{ asset('login-assets/images/logo.svg') }}" alt="logo" class="logo">
</div>
<p class="login-card-description">Sign into your account</p>

<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="form-group">
        <label for="email" class="sr-only">Email</label>
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email"
            value="{{ old('email') }}" required autocomplete="email" autofocus>

        @error('email')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>

    <div class="form-group mb-4">
        <label for="password" class="sr-only">Password</label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
            name="password" required autocomplete="current-password">

        @error('password')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>

    <div class="form-group mb-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked'
                : '' }}>

            <label class="form-check-label" for="remember">
                {{ __('Remember Me') }}
            </label>
        </div>
    </div>

    <button type="submit" class="btn btn-block login-btn mb-4">
        {{ __('Login') }}
    </button>
</form>

@if (Route::has('password.request'))
<a class="forgot-password-link" href="{{ route('password.request') }}">
    {{ __('Forgot Your Password?') }}
</a>
@endif
@endsection