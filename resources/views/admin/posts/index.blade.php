@extends('admin.layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-wrapper-before"></div>
    <div class="content-header row">
        <div class="content-header-left col-md-4 col-12 mb-2">
            <h3 class="content-header-title">Forum Posts Moderation</h3>
        </div>
    </div>
    <div class="content-body">
        @include('admin.layouts._flash')
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Posts</h4>
                        <div class="card-actions">
                            <form method="GET" action="{{ route('admin.posts.index') }}" class="d-inline-flex align-items-center">
                                <select name="topic_id" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                                    <option value="">All Topics</option>
                                    @foreach($topics as $topic)
                                        <option value="{{ $topic->id }}" {{ request('topic_id') == $topic->id ? 'selected' : '' }}>
                                            {{ Str::limit($topic->title, 50) }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="text" name="search" class="form-control form-control-sm mr-2" placeholder="Search content..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="ft-search"></i>
                                </button>
                                @if(request('topic_id') || request('search'))
                                    <a href="{{ route('admin.posts.index') }}" class="btn btn-sm btn-secondary ml-2">
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
                                            <th>Topic</th>
                                            <th>Author</th>
                                            <th>Content</th>
                                            <th>Type</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($posts as $post)
                                        <tr>
                                            <td>{{ $post->id }}</td>
                                            <td>
                                                <a href="{{ route('admin.topics.show', $post->topic_id) }}" class="text-primary">
                                                    {{ Str::limit($post->topic->title, 40) }}
                                                </a>
                                            </td>
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
                                                        <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this post?')">
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
                                            <td colspan="7" class="text-center">No posts found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            {{ $posts->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

