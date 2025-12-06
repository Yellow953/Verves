<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use Illuminate\Http\Request;

class ExerciseController extends Controller
{
    /**
     * Display a listing of exercises.
     */
    public function index(Request $request)
    {
        $query = Exercise::query();

        // Search
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by muscle group
        if ($request->has('muscle_group') && $request->muscle_group) {
            $query->where('muscle_group', $request->muscle_group);
        }

        // Filter by equipment
        if ($request->has('equipment') && $request->equipment) {
            $query->where('equipment', $request->equipment);
        }

        // Filter by difficulty
        if ($request->has('difficulty') && $request->difficulty) {
            $query->where('difficulty', $request->difficulty);
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $exercises = $query->orderBy('name', 'asc')->paginate(20);
        
        // Get unique values for filters
        $muscleGroups = Exercise::distinct()->whereNotNull('muscle_group')->pluck('muscle_group')->sort()->values();
        $equipmentTypes = Exercise::distinct()->whereNotNull('equipment')->pluck('equipment')->sort()->values();

        return view('admin.exercises.index', compact('exercises', 'muscleGroups', 'equipmentTypes'));
    }

    /**
     * Show the form for creating a new exercise.
     */
    public function create()
    {
        return view('admin.exercises.create');
    }

    /**
     * Store a newly created exercise.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'muscle_group' => 'nullable|string|max:255',
            'equipment' => 'nullable|string|max:255',
            'difficulty' => 'required|in:beginner,intermediate,advanced',
            'instructions' => 'nullable|string',
            'video_urls' => 'nullable|array',
            'video_urls.*' => 'url',
            'images' => 'nullable|array',
            'images.*' => 'url',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        Exercise::create($validated);

        return redirect()->route('admin.exercises.index')
            ->with('success', 'Exercise created successfully.');
    }

    /**
     * Display the specified exercise.
     */
    public function show(Exercise $exercise)
    {
        return view('admin.exercises.show', compact('exercise'));
    }

    /**
     * Show the form for editing the specified exercise.
     */
    public function edit(Exercise $exercise)
    {
        return view('admin.exercises.edit', compact('exercise'));
    }

    /**
     * Update the specified exercise.
     */
    public function update(Request $request, Exercise $exercise)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'muscle_group' => 'nullable|string|max:255',
            'equipment' => 'nullable|string|max:255',
            'difficulty' => 'required|in:beginner,intermediate,advanced',
            'instructions' => 'nullable|string',
            'video_urls' => 'nullable|array',
            'video_urls.*' => 'url',
            'images' => 'nullable|array',
            'images.*' => 'url',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $exercise->update($validated);

        return redirect()->route('admin.exercises.index')
            ->with('success', 'Exercise updated successfully.');
    }

    /**
     * Remove the specified exercise.
     */
    public function destroy(Exercise $exercise)
    {
        $exercise->delete();

        return redirect()->route('admin.exercises.index')
            ->with('success', 'Exercise deleted successfully.');
    }
}

