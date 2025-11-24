@extends('auth.layouts.app')

@section('content')
<div class="brand-wrapper">
    <img src="{{ asset('login-assets/images/logo.svg') }}" alt="logo" class="logo">
</div>
<p class="login-card-description">Reset your password</p>

<form method="POST" action="{{ route('password.update') }}">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">

    <div class="form-group">
        <label for="email" class="sr-only">Email</label>
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email"
            value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus placeholder="Email address">

        @error('email')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>

    <div class="form-group">
        <label for="password" class="sr-only">Password</label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password"
            required autocomplete="new-password" placeholder="New password">

        @error('password')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>

    <div class="form-group mb-4">
        <label for="password-confirm" class="sr-only">Confirm Password</label>
        <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
            required autocomplete="new-password" placeholder="Confirm password">
    </div>

    <button type="submit" class="btn btn-block login-btn mb-4">
        {{ __('Reset Password') }}
    </button>
</form>

<p class="login-card-footer-text">Remember your password? <a href="{{ route('login') }}" class="text-reset">Login here</a></p>
@endsection