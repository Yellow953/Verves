@extends('admin.layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-wrapper-before"></div>
    <div class="content-header row">
        <div class="content-header-left col-md-4 col-12 mb-2">
            <h3 class="content-header-title">Booking Details</h3>
        </div>
    </div>
    <div class="content-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Booking #{{ $booking->id }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <p><strong>Coach:</strong> {{ $booking->coach->name }}</p>
                            <p><strong>Client:</strong> {{ $booking->client->name }}</p>
                            <p><strong>Session Date:</strong> {{ $booking->session_date->format('M d, Y H:i') }}</p>
                            <p><strong>Duration:</strong> {{ $booking->duration_minutes }} minutes</p>
                            <p><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $booking->session_type)) }}</p>
                            <p><strong>Status:</strong> <span class="badge badge-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'cancelled' ? 'danger' : 'warning') }}">{{ ucfirst($booking->status) }}</span></p>
                            <p><strong>Location:</strong> {{ $booking->location ?? 'N/A' }}</p>
                            <p><strong>Price:</strong> ${{ number_format($booking->price ?? 0, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

