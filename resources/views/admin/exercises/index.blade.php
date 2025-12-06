@extends('admin.layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-wrapper-before"></div>
    <div class="content-header row">
        <div class="content-header-left col-md-4 col-12 mb-2">
            <h3 class="content-header-title">Exercises Library</h3>
        </div>
        <div class="content-header-right col-md-8 col-12">
            <div class="breadcrumbs-top float-md-right">
                <div class="breadcrumb-wrapper mr-1">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Exercises</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        @include('admin.layouts._flash')
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Exercises</h4>
                        <div class="heading-elements">
                            <a href="{{ route('admin.exercises.create') }}" class="btn btn-primary">
                                <i class="ft-plus"></i> Add Exercise
                            </a>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <!-- Filters -->
                            <form method="GET" action="{{ route('admin.exercises.index') }}" class="mb-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <input type="text" name="search" class="form-control" placeholder="Search by name..." value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <select name="muscle_group" class="form-control">
                                            <option value="">All Muscle Groups</option>
                                            @foreach($muscleGroups as $mg)
                                                <option value="{{ $mg }}" {{ request('muscle_group') == $mg ? 'selected' : '' }}>{{ $mg }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="equipment" class="form-control">
                                            <option value="">All Equipment</option>
                                            @foreach($equipmentTypes as $eq)
                                                <option value="{{ $eq }}" {{ request('equipment') == $eq ? 'selected' : '' }}>{{ $eq }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="difficulty" class="form-control">
                                            <option value="">All Difficulties</option>
                                            <option value="beginner" {{ request('difficulty') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                            <option value="intermediate" {{ request('difficulty') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                            <option value="advanced" {{ request('difficulty') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="status" class="form-control">
                                            <option value="">All Status</option>
                                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Muscle Group</th>
                                            <th>Equipment</th>
                                            <th>Difficulty</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($exercises as $exercise)
                                            <tr>
                                                <td>{{ $exercise->id }}</td>
                                                <td><strong>{{ $exercise->name }}</strong></td>
                                                <td>{{ $exercise->muscle_group ?? 'N/A' }}</td>
                                                <td>{{ $exercise->equipment ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $exercise->difficulty == 'beginner' ? 'success' : ($exercise->difficulty == 'intermediate' ? 'warning' : 'danger') }}">
                                                        {{ ucfirst($exercise->difficulty) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-{{ $exercise->is_active ? 'success' : 'secondary' }}">
                                                        {{ $exercise->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.exercises.show', $exercise) }}" class="btn btn-sm btn-info" title="View">
                                                            <i class="ft-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.exercises.edit', $exercise) }}" class="btn btn-sm btn-warning" title="Edit">
                                                            <i class="ft-edit"></i>
                                                        </a>
                                                        <form action="{{ route('admin.exercises.destroy', $exercise) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this exercise?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                                <i class="ft-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">No exercises found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-2">
                                {{ $exercises->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

