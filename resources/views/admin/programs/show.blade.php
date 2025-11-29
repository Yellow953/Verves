@extends('admin.layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-wrapper-before"></div>
    <div class="content-header row">
        <div class="content-header-left col-md-4 col-12 mb-2">
            <h3 class="content-header-title">Program Details</h3>
        </div>
    </div>
    <div class="content-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ $program->name }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <p><strong>Description:</strong> {{ $program->description ?? 'N/A' }}</p>
                            <p><strong>Coach:</strong> {{ $program->coach->name ?? 'N/A' }}</p>
                            <p><strong>Client:</strong> {{ $program->client->name ?? 'N/A' }}</p>
                            <p><strong>Type:</strong> {{ ucfirst($program->type) }}</p>
                            <p><strong>Status:</strong> <span class="badge badge-{{ $program->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($program->status) }}</span></p>
                            <p><strong>Duration:</strong> {{ $program->duration_weeks ?? 'N/A' }} weeks</p>
                            <p><strong>Exercises:</strong> {{ $program->exercises->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

