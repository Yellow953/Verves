@extends('admin.layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-wrapper-before"></div>
    <div class="content-header row">
        <div class="content-header-left col-md-4 col-12 mb-2">
            <h3 class="content-header-title">Post Details</h3>
        </div>
        <div class="content-header-right col-md-8 col-12">
            <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary float-md-right">Back to Posts</a>
        </div>
    </div>
    <div class="content-body">
        @include('admin.layouts._flash')
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Post #{{ $post->id }}</h4>
                        <div class="card-actions">
                            <a href="{{ route('admin.topics.show', $post->topic_id) }}" class="btn btn-sm btn-info">
                                <i class="ft-eye"></i> View Topic
                            </a>
                            <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn btn-sm btn-warning">
                                <i class="ft-edit"></i> Edit
                            </a>
                            @if(!$post->is_first_post)
                                <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this post?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="ft-trash"></i> Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p><strong>Topic:</strong> 
                                        <a href="{{ route('admin.topics.show', $post->topic_id) }}" class="text-primary">
                                            {{ $post->topic->title }}
                                        </a>
                                    </p>
                                    <p><strong>Author:</strong> {{ $post->user->name }} ({{ $post->user->email }})</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Type:</strong> 
                                        @if($post->is_first_post)
                                            <span class="badge badge-primary">First Post</span>
                                        @else
                                            <span class="badge badge-secondary">Reply</span>
                                        @endif
                                    </p>
                                    <p><strong>Created:</strong> {{ $post->created_at->format('M d, Y H:i') }}</p>
                                    <p><strong>Updated:</strong> {{ $post->updated_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                            <hr>
                            <div class="post-content">
                                <h5>Content:</h5>
                                <div class="p-3 bg-light rounded">
                                    {!! nl2br(e($post->body)) !!}
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

