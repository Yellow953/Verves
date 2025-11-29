<div class="main-menu menu-fixed menu-light menu-accordion    menu-shadow " data-scroll-to-active="true"
    data-img="{{ asset('admin-assets/images/backgrounds/02.jpg') }}">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto"><a class="navbar-brand" href="{{ route('admin.dashboard') }}"><img class="brand-logo"
                        alt="Verve admin logo" src="{{ asset('admin-assets/images/logo/logo.png') }}" />
                    <h3 class="brand-text">Verve</h3>
                </a></li>
            <li class="nav-item d-md-none"><a class="nav-link close-navbar"><i class="ft-x"></i></a></li>
        </ul>
    </div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class="nav-item"><a href="{{ route('admin.dashboard') }}"><i class="ft-home"></i><span class="menu-title">Dashboard</span></a></li>
            <li class="nav-item"><a href="{{ route('admin.users.index') }}"><i class="ft-users"></i><span class="menu-title">Users</span></a></li>
            <li class="nav-item"><a href="{{ route('admin.programs.index') }}"><i class="ft-layers"></i><span class="menu-title">Programs</span></a></li>
            <li class="nav-item"><a href="{{ route('admin.bookings.index') }}"><i class="ft-calendar"></i><span class="menu-title">Bookings</span></a></li>
            <li class="nav-item"><a href="{{ route('admin.subscriptions.index') }}"><i class="ft-credit-card"></i><span class="menu-title">Subscriptions</span></a></li>
            <li class="nav-item"><a href="{{ route('admin.categories.index') }}"><i class="ft-folder"></i><span class="menu-title">Forum Categories</span></a></li>
        </ul>
    </div>
    <div class="navigation-background"></div>
</div>