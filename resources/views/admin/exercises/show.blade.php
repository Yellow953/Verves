@extends('admin.layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-wrapper-before"></div>
    <div class="content-header row">
        <div class="content-header-left col-md-4 col-12 mb-2">
            <h3 class="content-header-title">Exercise Details</h3>
        </div>
        <div class="content-header-right col-md-8 col-12">
            <div class="breadcrumbs-top float-md-right">
                <div class="breadcrumb-wrapper mr-1">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.exercises.index') }}">Exercises</a></li>
                        <li class="breadcrumb-item active">View</li>
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
                        <h4 class="card-title">{{ $exercise->name }}</h4>
                        <div class="heading-elements">
                            <a href="{{ route('admin.exercises.edit', $exercise) }}" class="btn btn-warning">
                                <i class="ft-edit"></i> Edit
                            </a>
                            <a href="{{ route('admin.exercises.index') }}" class="btn btn-secondary">
                                <i class="ft-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="150">Name:</th>
                                            <td><strong>{{ $exercise->name }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Muscle Group:</th>
                                            <td>{{ $exercise->muscle_group ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Equipment:</th>
                                            <td>{{ $exercise->equipment ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Difficulty:</th>
                                            <td>
                                                <span class="badge badge-{{ $exercise->difficulty == 'beginner' ? 'success' : ($exercise->difficulty == 'intermediate' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($exercise->difficulty) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Status:</th>
                                            <td>
                                                <span class="badge badge-{{ $exercise->is_active ? 'success' : 'secondary' }}">
                                                    {{ $exercise->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($exercise->description)
                                <div class="mt-3">
                                    <h5>Description</h5>
                                    <p>{{ $exercise->description }}</p>
                                </div>
                            @endif

                            @if($exercise->instructions)
                                <div class="mt-3">
                                    <h5>Instructions</h5>
                                    <p>{{ $exercise->instructions }}</p>
                                </div>
                            @endif

                            @if($exercise->video_urls && count($exercise->video_urls) > 0)
                                <div class="mt-3">
                                    <h5>Video URLs</h5>
                                    <ul>
                                        @foreach($exercise->video_urls as $url)
                                            <li><a href="{{ $url }}" target="_blank">{{ $url }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if($exercise->images && count($exercise->images) > 0)
                                <div class="mt-3">
                                    <h5>Images</h5>
                                    <div class="row">
                                        @foreach($exercise->images as $image)
                                            <div class="col-md-3 mb-2">
                                                <img src="{{ $image }}" alt="{{ $exercise->name }}" class="img-thumbnail" style="max-width: 100%;">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

