@extends('auth.layouts.app')

@section('content')
<div class="form-header">
    <h2 class="form-title">Sign In</h2>
    <p class="form-subtitle">Enter your credentials to access the admin panel</p>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <div>
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    </div>
@endif

<form method="POST" action="{{ route('login') }}" class="login-form">
    @csrf

    <div class="form-group">
        <label for="email" class="form-label">
            <i class="fas fa-envelope"></i>
            Email Address
        </label>
        <input 
            id="email" 
            type="email" 
            class="form-input @error('email') is-invalid @enderror" 
            name="email"
            value="{{ old('email') }}" 
            required 
            autocomplete="email" 
            autofocus
            placeholder="admin@example.com"
        >
        @error('email')
        <span class="error-message" role="alert">
            <i class="fas fa-exclamation-circle"></i>
            {{ $message }}
        </span>
        @enderror
    </div>

    <div class="form-group">
        <label for="password" class="form-label">
            <i class="fas fa-lock"></i>
            Password
        </label>
        <div class="password-input-wrapper">
            <input 
                id="password" 
                type="password" 
                class="form-input @error('password') is-invalid @enderror"
                name="password" 
                required 
                autocomplete="current-password"
                placeholder="Enter your password"
            >
            <button type="button" class="password-toggle" onclick="togglePassword()">
                <i class="fas fa-eye" id="password-toggle-icon"></i>
            </button>
        </div>
        @error('password')
        <span class="error-message" role="alert">
            <i class="fas fa-exclamation-circle"></i>
            {{ $message }}
        </span>
        @enderror
    </div>

    <div class="form-options">
        <div class="form-check">
            <input 
                class="form-check-input" 
                type="checkbox" 
                name="remember" 
                id="remember" 
                {{ old('remember') ? 'checked' : '' }}
            >
            <label class="form-check-label" for="remember">
                Remember me
            </label>
        </div>
        @if (Route::has('password.request'))
        <a class="forgot-password-link" href="{{ route('password.request') }}">
            Forgot password?
        </a>
        @endif
    </div>

    <button type="submit" class="login-btn">
        <span>Sign In</span>
        <i class="fas fa-arrow-right"></i>
    </button>
</form>

<div class="form-footer">
    <p class="footer-text">
        <i class="fas fa-info-circle"></i>
        Secure admin access only
    </p>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('password-toggle-icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}
</script>
@endsection