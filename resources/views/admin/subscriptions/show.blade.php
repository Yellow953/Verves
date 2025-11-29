@extends('admin.layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-wrapper-before"></div>
    <div class="content-header row">
        <div class="content-header-left col-md-4 col-12 mb-2">
            <h3 class="content-header-title">Subscription Details</h3>
        </div>
    </div>
    <div class="content-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ $subscription->plan_name }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <p><strong>Client:</strong> {{ $subscription->client->name }}</p>
                            <p><strong>Coach:</strong> {{ $subscription->coach->name }}</p>
                            <p><strong>Price:</strong> ${{ number_format($subscription->price, 2) }} {{ $subscription->currency }}</p>
                            <p><strong>Billing Cycle:</strong> {{ ucfirst($subscription->billing_cycle) }}</p>
                            <p><strong>Status:</strong> <span class="badge badge-{{ $subscription->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($subscription->status) }}</span></p>
                            <p><strong>Start Date:</strong> {{ $subscription->start_date->format('M d, Y') }}</p>
                            <p><strong>End Date:</strong> {{ $subscription->end_date ? $subscription->end_date->format('M d, Y') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

