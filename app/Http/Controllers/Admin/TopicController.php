<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Models\Category;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function index(Request $request)
    {
        $query = Topic::with(['user:id,name,email', 'category:id,name'])
            ->withCount('posts');

        // Filter by category
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'pinned') {
                $query->where('is_pinned', true);
            } elseif ($request->status === 'locked') {
                $query->where('is_locked', true);
            } elseif ($request->status === 'active') {
                $query->where('is_pinned', false)->where('is_locked', false);
            }
        }

        // Search
        if ($request->has('search') && $request->search) {
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

        $topics = $query->paginate(20);
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.topics.index', compact('topics', 'categories'));
    }

    public function show($id)
    {
        $topic = Topic::with(['user:id,name,email', 'category:id,name,slug', 'posts.user:id,name,email'])
            ->withCount('posts')
            ->findOrFail($id);

        return view('admin.topics.show', compact('topic'));
    }

    public function edit($id)
    {
        $topic = Topic::with(['user:id,name,email', 'category:id,name'])->findOrFail($id);
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.topics.edit', compact('topic', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $topic = Topic::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'is_pinned' => 'nullable|boolean',
            'is_locked' => 'nullable|boolean',
        ]);

        $topic->update($validated);

        // Update first post body if body is provided
        if ($request->has('body')) {
            $firstPost = $topic->posts()->where('is_first_post', true)->first();
            if ($firstPost) {
                $firstPost->update(['body' => $validated['body']]);
            }
        }

        return redirect()->route('admin.topics.show', $topic->id)
            ->with('success', 'Topic updated successfully');
    }

    public function destroy($id)
    {
        $topic = Topic::findOrFail($id);
        $topic->delete();

        return redirect()->route('admin.topics.index')
            ->with('success', 'Topic deleted successfully');
    }

    public function togglePin($id)
    {
        $topic = Topic::findOrFail($id);
        $topic->update(['is_pinned' => !$topic->is_pinned]);

        $message = $topic->is_pinned ? 'Topic pinned successfully' : 'Topic unpinned successfully';

        return redirect()->back()->with('success', $message);
    }

    public function toggleLock($id)
    {
        $topic = Topic::findOrFail($id);
        $topic->update(['is_locked' => !$topic->is_locked]);

        $message = $topic->is_locked ? 'Topic locked successfully' : 'Topic unlocked successfully';

        return redirect()->back()->with('success', $message);
    }
}

