@extends('admin.layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-wrapper-before"></div>
    <div class="content-header row">
        <div class="content-header-left col-md-4 col-12 mb-2">
            <h3 class="content-header-title">Forum Topics Moderation</h3>
        </div>
    </div>
    <div class="content-body">
        @include('admin.layouts._flash')
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Topics</h4>
                        <div class="card-actions">
                            <form method="GET" action="{{ route('admin.topics.index') }}" class="d-inline-flex align-items-center">
                                <select name="category_id" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <select name="status" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                                    <option value="">All Status</option>
                                    <option value="pinned" {{ request('status') == 'pinned' ? 'selected' : '' }}>Pinned</option>
                                    <option value="locked" {{ request('status') == 'locked' ? 'selected' : '' }}>Locked</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                </select>
                                <input type="text" name="search" class="form-control form-control-sm mr-2" placeholder="Search..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="ft-search"></i>
                                </button>
                                @if(request('category_id') || request('status') || request('search'))
                                    <a href="{{ route('admin.topics.index') }}" class="btn btn-sm btn-secondary ml-2">
                                        <i class="ft-x"></i> Clear
                                    </a>
                                @endif
                            </form>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Title</th>
                                            <th>Category</th>
                                            <th>Author</th>
                                            <th>Replies</th>
                                            <th>Views</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($topics as $topic)
                                        <tr>
                                            <td>{{ $topic->id }}</td>
                                            <td>
                                                <a href="{{ route('admin.topics.show', $topic->id) }}" class="text-primary">
                                                    {{ Str::limit($topic->title, 50) }}
                                                </a>
                                                @if($topic->is_pinned)
                                                    <i class="ft-paperclip text-warning ml-1" title="Pinned"></i>
                                                @endif
                                                @if($topic->is_locked)
                                                    <i class="ft-lock text-danger ml-1" title="Locked"></i>
                                                @endif
                                            </td>
                                            <td>{{ $topic->category->name }}</td>
                                            <td>{{ $topic->user->name }}</td>
                                            <td>{{ $topic->posts_count }}</td>
                                            <td>{{ $topic->views_count }}</td>
                                            <td>
                                                @if($topic->is_pinned)
                                                    <span class="badge badge-warning">Pinned</span>
                                                @endif
                                                @if($topic->is_locked)
                                                    <span class="badge badge-danger">Locked</span>
                                                @endif
                                                @if(!$topic->is_pinned && !$topic->is_locked)
                                                    <span class="badge badge-success">Active</span>
                                                @endif
                                            </td>
                                            <td>{{ $topic->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.topics.show', $topic->id) }}" class="btn btn-sm btn-info" title="View">
                                                        <i class="ft-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.topics.edit', $topic->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="ft-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.topics.toggle-pin', $topic->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-{{ $topic->is_pinned ? 'secondary' : 'warning' }}" title="{{ $topic->is_pinned ? 'Unpin' : 'Pin' }}">
                                                            <i class="ft-paperclip"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.topics.toggle-lock', $topic->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-{{ $topic->is_locked ? 'secondary' : 'danger' }}" title="{{ $topic->is_locked ? 'Unlock' : 'Lock' }}">
                                                            <i class="ft-lock"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.topics.destroy', $topic->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this topic?')">
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
                                            <td colspan="9" class="text-center">No topics found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            {{ $topics->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

