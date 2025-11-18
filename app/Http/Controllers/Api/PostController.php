<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    /**
     * Display a listing of posts for a topic.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'topic_id' => 'required|exists:topics,id',
        ]);

        $query = Post::with(['user:id,name,email'])
            ->where('topic_id', $request->topic_id)
            ->orderBy('created_at', 'asc');

        $perPage = $request->get('per_page', 15);
        $posts = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $posts,
        ]);
    }

    /**
     * Store a newly created post.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'topic_id' => 'required|exists:topics,id',
            'body' => 'required|string',
        ]);

        $topic = Topic::findOrFail($validated['topic_id']);

        // Check if topic is locked
        if ($topic->is_locked && $request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'This topic is locked',
            ], 403);
        }

        $post = Post::create([
            'topic_id' => $validated['topic_id'],
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
            'is_first_post' => false,
        ]);

        $post->load(['user:id,name,email', 'topic:id,title']);

        return response()->json([
            'success' => true,
            'message' => 'Post created successfully',
            'data' => $post,
        ], 201);
    }

    /**
     * Display the specified post.
     */
    public function show(string $id): JsonResponse
    {
        $post = Post::with(['user:id,name,email', 'topic:id,title,slug'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $post,
        ]);
    }

    /**
     * Update the specified post.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $post = Post::findOrFail($id);

        // Check if user owns the post or is admin
        if ($post->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this post',
            ], 403);
        }

        // Check if topic is locked (unless admin)
        if ($post->topic->is_locked && $request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'This topic is locked',
            ], 403);
        }

        $validated = $request->validate([
            'body' => 'required|string',
        ]);

        $post->update($validated);
        $post->load(['user:id,name,email', 'topic:id,title,slug']);

        return response()->json([
            'success' => true,
            'message' => 'Post updated successfully',
            'data' => $post,
        ]);
    }

    /**
     * Remove the specified post.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $post = Post::findOrFail($id);

        // Check if user owns the post or is admin
        if ($post->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this post',
            ], 403);
        }

        // Prevent deleting the first post (delete the topic instead)
        if ($post->is_first_post) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete the first post. Delete the topic instead.',
            ], 403);
        }

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully',
        ]);
    }
}
