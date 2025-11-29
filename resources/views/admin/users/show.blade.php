@extends('admin.layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-wrapper-before"></div>
    <div class="content-header row">
        <div class="content-header-left col-md-4 col-12 mb-2">
            <h3 class="content-header-title">User Details</h3>
        </div>
        <div class="content-header-right col-md-8 col-12">
            <div class="breadcrumbs-top float-md-right">
                <div class="breadcrumb-wrapper mr-1">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                        <li class="breadcrumb-item active">Details</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ $user->name }}</h4>
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning">Edit User</a>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Email:</strong> {{ $user->email }}</p>
                                    <p><strong>Phone:</strong> {{ $user->phone ?? 'N/A' }}</p>
                                    <p><strong>Type:</strong> <span class="badge badge-{{ $user->type === 'admin' ? 'danger' : ($user->type === 'coach' ? 'primary' : 'success') }}">{{ ucfirst($user->type) }}</span></p>
                                    <p><strong>Role:</strong> {{ $user->role ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Bio:</strong> {{ $user->bio ?? 'N/A' }}</p>
                                    <p><strong>Specialization:</strong> {{ $user->specialization ?? 'N/A' }}</p>
                                    <p><strong>Joined:</strong> {{ $user->created_at->format('M d, Y H:i') }}</p>
                                    <p><strong>Last Updated:</strong> {{ $user->updated_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

