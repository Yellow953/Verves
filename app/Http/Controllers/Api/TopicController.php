<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Topic;
use App\Models\TopicView;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class TopicController extends Controller
{
    /**
     * Display a listing of topics.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Topic::with(['user:id,name,email', 'category:id,name,slug'])
            ->withCount('posts');

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%");
            });
        }

        // Order by pinned first, then by last_reply_at or created_at
        $query->orderBy('is_pinned', 'desc')
            ->orderBy('last_reply_at', 'desc')
            ->orderBy('created_at', 'desc');

        $perPage = $request->get('per_page', 15);
        $topics = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $topics,
        ]);
    }

    /**
     * Store a newly created topic.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'slug' => 'nullable|string|max:255|unique:topics,slug',
        ]);

        $validated['user_id'] = $request->user()->id;
        
        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
            // Ensure uniqueness
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Topic::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $topic = Topic::create($validated);

        // Create the first post
        $topic->posts()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
            'is_first_post' => true,
        ]);

        $topic->load(['user:id,name,email', 'category:id,name,slug']);

        return response()->json([
            'success' => true,
            'message' => 'Topic created successfully',
            'data' => $topic,
        ], 201);
    }

    /**
     * Display the specified topic.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $topic = Topic::with([
            'user:id,name,email',
            'category:id,name,slug',
            'posts.user:id,name,email',
        ])
            ->withCount('posts')
            ->findOrFail($id);

        // Track view
        $this->trackView($request, $topic);

        return response()->json([
            'success' => true,
            'data' => $topic,
        ]);
    }

    /**
     * Update the specified topic.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $topic = Topic::findOrFail($id);

        // Check if user owns the topic or is admin
        if ($topic->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this topic',
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'body' => 'sometimes|required|string',
            'category_id' => 'sometimes|required|exists:categories,id',
            'is_pinned' => 'sometimes|boolean',
            'is_locked' => 'sometimes|boolean',
        ]);

        // Update topic
        $topic->update($validated);

        // Update first post body if body is provided
        if ($request->has('body')) {
            $firstPost = $topic->posts()->where('is_first_post', true)->first();
            if ($firstPost) {
                $firstPost->update(['body' => $validated['body']]);
            }
        }

        $topic->load(['user:id,name,email', 'category:id,name,slug']);

        return response()->json([
            'success' => true,
            'message' => 'Topic updated successfully',
            'data' => $topic,
        ]);
    }

    /**
     * Remove the specified topic.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $topic = Topic::findOrFail($id);

        // Check if user owns the topic or is admin
        if ($topic->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this topic',
            ], 403);
        }

        $topic->delete();

        return response()->json([
            'success' => true,
            'message' => 'Topic deleted successfully',
        ]);
    }

    /**
     * Track a view for the topic.
     */
    private function trackView(Request $request, Topic $topic): void
    {
        $userId = $request->user()?->id;
        $ipAddress = $request->ip();

        // Check if view already exists (prevent duplicate views)
        $exists = TopicView::where('topic_id', $topic->id)
            ->where(function ($query) use ($userId, $ipAddress) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->whereNull('user_id')
                        ->where('ip_address', $ipAddress);
                }
            })
            ->where('viewed_at', '>', now()->subHour())
            ->exists();

        if (!$exists) {
            TopicView::create([
                'topic_id' => $topic->id,
                'user_id' => $userId,
                'ip_address' => $ipAddress,
                'viewed_at' => now(),
            ]);

            $topic->incrementViews();
        }
    }
}
