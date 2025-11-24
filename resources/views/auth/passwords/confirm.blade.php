@extends('auth.layouts.app')

@section('content')
<div class="brand-wrapper">
    <img src="{{ asset('login-assets/images/logo.svg') }}" alt="logo" class="logo">
</div>
<p class="login-card-description">Please confirm your password before continuing.</p>

<form method="POST" action="{{ route('password.confirm') }}">
    @csrf

    <div class="form-group mb-4">
        <label for="password" class="sr-only">Password</label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password"
            required autocomplete="current-password" placeholder="Password">

        @error('password')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>

    <button type="submit" class="btn btn-block login-btn mb-4">
        {{ __('Confirm Password') }}
    </button>
</form>

@if (Route::has('password.request'))
<a class="forgot-password-link" href="{{ route('password.request') }}">
    {{ __('Forgot Your Password?') }}
</a>
@endif
@endsection