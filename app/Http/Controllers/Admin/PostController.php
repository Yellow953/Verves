<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Topic;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['user:id,name,email', 'topic:id,title,slug']);

        // Filter by topic
        if ($request->has('topic_id') && $request->topic_id) {
            $query->where('topic_id', $request->topic_id);
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('body', 'like', "%{$search}%");
        }

        $query->orderBy('created_at', 'desc');

        $posts = $query->paginate(20);
        $topics = Topic::orderBy('title')->get(['id', 'title']);

        return view('admin.posts.index', compact('posts', 'topics'));
    }

    public function show($id)
    {
        $post = Post::with(['user:id,name,email', 'topic:id,title,slug'])->findOrFail($id);

        return view('admin.posts.show', compact('post'));
    }

    public function edit($id)
    {
        $post = Post::with(['user:id,name,email', 'topic:id,title'])->findOrFail($id);

        return view('admin.posts.edit', compact('post'));
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $validated = $request->validate([
            'body' => 'required|string',
        ]);

        $post->update($validated);

        return redirect()->route('admin.posts.show', $post->id)
            ->with('success', 'Post updated successfully');
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        // Prevent deleting the first post (delete the topic instead)
        if ($post->is_first_post) {
            return redirect()->back()
                ->with('error', 'Cannot delete the first post. Delete the topic instead.');
        }

        $topicId = $post->topic_id;
        $post->delete();

        return redirect()->route('admin.topics.show', $topicId)
            ->with('success', 'Post deleted successfully');
    }
}

