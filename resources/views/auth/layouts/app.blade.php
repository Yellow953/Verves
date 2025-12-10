<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Verve | Admin Login</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('login-assets/css/login.css') }}">
</head>

<body>
    <div class="login-wrapper">
        <div class="login-container">
            <div class="login-card">
                <!-- Left Side - Branding -->
                <div class="login-branding">
                    <div class="branding-content">
                        <div class="brand-logo">
                            <span class="logo-text">Verve</span>
                            <span class="logo-accent">s</span>
                        </div>
                        <h1 class="branding-title">Admin Portal</h1>
                        <p class="branding-subtitle">Welcome back! Please sign in to your account.</p>
                        <div class="branding-features">
                            <div class="feature-item">
                                <i class="fas fa-shield-alt"></i>
                                <span>Secure Access</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-chart-line"></i>
                                <span>Analytics Dashboard</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-users-cog"></i>
                                <span>User Management</span>
                            </div>
                        </div>
                    </div>
                    <div class="branding-pattern"></div>
                </div>

                <!-- Right Side - Login Form -->
                <div class="login-form-container">
                    <div class="login-form-wrapper">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>