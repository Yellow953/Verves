@extends('auth.layouts.app')

@section('content')
<div class="brand-wrapper">
    <img src="{{ asset('login-assets/images/logo.svg') }}" alt="logo" class="logo">
</div>
<p class="login-card-description">Reset your password</p>

@if (session('status'))
<div class="alert alert-success" role="alert">
    {{ session('status') }}
</div>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf

    <div class="form-group">
        <label for="email" class="sr-only">Email</label>
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email"
            value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Email address">

        @error('email')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>

    <button type="submit" class="btn btn-block login-btn mb-4">
        {{ __('Send Password Reset Link') }}
    </button>
</form>

<p class="login-card-footer-text">Remember your password? <a href="{{ route('login') }}">Login here</a></p>
@endsection