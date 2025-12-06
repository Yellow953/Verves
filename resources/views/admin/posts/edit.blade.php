@extends('admin.layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-wrapper-before"></div>
    <div class="content-header row">
        <div class="content-header-left col-md-4 col-12 mb-2">
            <h3 class="content-header-title">Edit Post</h3>
        </div>
        <div class="content-header-right col-md-8 col-12">
            <a href="{{ route('admin.posts.show', $post->id) }}" class="btn btn-secondary float-md-right">Back to Post</a>
        </div>
    </div>
    <div class="content-body">
        @include('admin.layouts._flash')
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit Post #{{ $post->id }}</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="mb-3">
                                <p><strong>Topic:</strong> 
                                    <a href="{{ route('admin.topics.show', $post->topic_id) }}" class="text-primary">
                                        {{ $post->topic->title }}
                                    </a>
                                </p>
                                <p><strong>Author:</strong> {{ $post->user->name }}</p>
                                <p><strong>Type:</strong> 
                                    @if($post->is_first_post)
                                        <span class="badge badge-primary">First Post</span>
                                    @else
                                        <span class="badge badge-secondary">Reply</span>
                                    @endif
                                </p>
                            </div>
                            
                            <form action="{{ route('admin.posts.update', $post->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="form-group">
                                    <label for="body">Content <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('body') is-invalid @enderror" id="body" name="body" rows="15" required>{{ old('body', $post->body) }}</textarea>
                                    @error('body')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Update Post</button>
                                    <a href="{{ route('admin.posts.show', $post->id) }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

