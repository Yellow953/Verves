@extends('admin.layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-wrapper-before"></div>
    <div class="content-header row">
        <div class="content-header-left col-md-4 col-12 mb-2">
            <h3 class="content-header-title">Dashboard</h3>
        </div>
        <div class="content-header-right col-md-8 col-12">
            <div class="breadcrumbs-top float-md-right">
                <div class="breadcrumb-wrapper mr-1">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Dashboard
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        @include('admin.layouts._flash')
        
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-xl-3 col-lg-6 col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="media d-flex">
                                <div class="align-self-center">
                                    <i class="la la-users font-large-2 float-left"></i>
                                </div>
                                <div class="media-body text-right">
                                    <h3>{{ $stats['total_users'] }}</h3>
                                    <span class="text-muted">Total Users</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="media d-flex">
                                <div class="align-self-center">
                                    <i class="la la-user-tie font-large-2 float-left"></i>
                                </div>
                                <div class="media-body text-right">
                                    <h3>{{ $stats['coaches'] }}</h3>
                                    <span class="text-muted">Coaches</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="media d-flex">
                                <div class="align-self-center">
                                    <i class="la la-calendar-check font-large-2 float-left"></i>
                                </div>
                                <div class="media-body text-right">
                                    <h3>{{ $stats['upcoming_bookings'] }}</h3>
                                    <span class="text-muted">Upcoming Bookings</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="media d-flex">
                                <div class="align-self-center">
                                    <i class="la la-dumbbell font-large-2 float-left"></i>
                                </div>
                                <div class="media-body text-right">
                                    <h3>{{ $stats['active_programs'] }}</h3>
                                    <span class="text-muted">Active Programs</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Users and Bookings -->
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Recent Users</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Type</th>
                                            <th>Joined</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recent_users as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td><span class="badge badge-{{ $user->type === 'admin' ? 'danger' : ($user->type === 'coach' ? 'primary' : 'success') }}">{{ ucfirst($user->type) }}</span></td>
                                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No users found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-primary btn-sm">View All Users</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Recent Bookings</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Coach</th>
                                            <th>Client</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recent_bookings as $booking)
                                        <tr>
                                            <td>{{ $booking->coach->name }}</td>
                                            <td>{{ $booking->client->name }}</td>
                                            <td>{{ $booking->session_date->format('M d, Y H:i') }}</td>
                                            <td><span class="badge badge-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'cancelled' ? 'danger' : 'warning') }}">{{ ucfirst($booking->status) }}</span></td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No bookings found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <a href="{{ route('admin.bookings.index') }}" class="btn btn-primary btn-sm">View All Bookings</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
