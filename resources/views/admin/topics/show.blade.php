@extends('admin.layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-wrapper-before"></div>
    <div class="content-header row">
        <div class="content-header-left col-md-4 col-12 mb-2">
            <h3 class="content-header-title">Topic Details</h3>
        </div>
        <div class="content-header-right col-md-8 col-12">
            <a href="{{ route('admin.topics.index') }}" class="btn btn-secondary float-md-right">Back to Topics</a>
        </div>
    </div>
    <div class="content-body">
        @include('admin.layouts._flash')
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ $topic->title }}</h4>
                        <div class="card-actions">
                            <form action="{{ route('admin.topics.toggle-pin', $topic->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-{{ $topic->is_pinned ? 'warning' : 'secondary' }}">
                                    <i class="ft-paperclip"></i> {{ $topic->is_pinned ? 'Unpin' : 'Pin' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.topics.toggle-lock', $topic->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-{{ $topic->is_locked ? 'danger' : 'secondary' }}">
                                    <i class="ft-lock"></i> {{ $topic->is_locked ? 'Unlock' : 'Lock' }}
                                </button>
                            </form>
                            <a href="{{ route('admin.topics.edit', $topic->id) }}" class="btn btn-sm btn-warning">
                                <i class="ft-edit"></i> Edit
                            </a>
                            <form action="{{ route('admin.topics.destroy', $topic->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this topic?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="ft-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <p><strong>Category:</strong> {{ $topic->category->name }}</p>
                                    <p><strong>Author:</strong> {{ $topic->user->name }} ({{ $topic->user->email }})</p>
                                    <p><strong>Created:</strong> {{ $topic->created_at->format('M d, Y H:i') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Replies:</strong> {{ $topic->posts_count }}</p>
                                    <p><strong>Views:</strong> {{ $topic->views_count }}</p>
                                    <p><strong>Last Reply:</strong> {{ $topic->last_reply_at ? $topic->last_reply_at->format('M d, Y H:i') : 'No replies yet' }}</p>
                                </div>
                            </div>
                            <div class="mb-3">
                                <strong>Status:</strong>
                                @if($topic->is_pinned)
                                    <span class="badge badge-warning">Pinned</span>
                                @endif
                                @if($topic->is_locked)
                                    <span class="badge badge-danger">Locked</span>
                                @endif
                                @if(!$topic->is_pinned && !$topic->is_locked)
                                    <span class="badge badge-success">Active</span>
                                @endif
                            </div>
                            <hr>
                            <h5>Posts in this Topic</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Author</th>
                                            <th>Content</th>
                                            <th>Type</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($topic->posts as $post)
                                        <tr>
                                            <td>{{ $post->id }}</td>
                                            <td>{{ $post->user->name }}</td>
                                            <td>{{ Str::limit(strip_tags($post->body), 100) }}</td>
                                            <td>
                                                @if($post->is_first_post)
                                                    <span class="badge badge-primary">First Post</span>
                                                @else
                                                    <span class="badge badge-secondary">Reply</span>
                                                @endif
                                            </td>
                                            <td>{{ $post->created_at->format('M d, Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.posts.show', $post->id) }}" class="btn btn-sm btn-info" title="View">
                                                        <i class="ft-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="ft-edit"></i>
                                                    </a>
                                                    @if(!$post->is_first_post)
                                                        <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                                <i class="ft-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No posts found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

