@extends('admin.layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-wrapper-before"></div>
    <div class="content-header row">
        <div class="content-header-left col-md-4 col-12 mb-2">
            <h3 class="content-header-title">Subscriptions Management</h3>
        </div>
    </div>
    <div class="content-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Subscriptions</h4>
                        <div class="heading-elements">
                            <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-primary">
                                <i class="ft-plus"></i> Add Subscription
                            </a>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Client</th>
                                            <th>Coach</th>
                                            <th>Plan</th>
                                            <th>Price</th>
                                            <th>Billing Cycle</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($subscriptions as $subscription)
                                        <tr>
                                            <td>{{ $subscription->id }}</td>
                                            <td>{{ $subscription->client->name }}</td>
                                            <td>{{ $subscription->coach->name }}</td>
                                            <td>{{ $subscription->plan_name }}</td>
                                            <td>${{ number_format($subscription->price, 2) }}</td>
                                            <td>{{ ucfirst($subscription->billing_cycle) }}</td>
                                            <td><span class="badge badge-{{ $subscription->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($subscription->status) }}</span></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.subscriptions.show', $subscription->id) }}" class="btn btn-sm btn-info" title="View">
                                                        <i class="ft-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No subscriptions found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            {{ $subscriptions->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

