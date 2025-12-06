<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ExerciseController extends Controller
{
    /**
     * Display a listing of exercises (public for coaches).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Exercise::active();

        // Filter by muscle group
        if ($request->has('muscle_group')) {
            $query->where('muscle_group', $request->muscle_group);
        }

        // Filter by equipment
        if ($request->has('equipment')) {
            $query->where('equipment', $request->equipment);
        }

        // Filter by difficulty
        if ($request->has('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->get('per_page', 50);
        $exercises = $query->orderBy('name', 'asc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $exercises,
        ]);
    }

    /**
     * Display the specified exercise.
     */
    public function show(string $id): JsonResponse
    {
        $exercise = Exercise::active()->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $exercise,
        ]);
    }

    /**
     * Get unique muscle groups.
     */
    public function muscleGroups(): JsonResponse
    {
        $muscleGroups = Exercise::active()
            ->distinct()
            ->whereNotNull('muscle_group')
            ->pluck('muscle_group')
            ->sort()
            ->values();

        return response()->json([
            'success' => true,
            'data' => $muscleGroups,
        ]);
    }

    /**
     * Get unique equipment types.
     */
    public function equipmentTypes(): JsonResponse
    {
        $equipmentTypes = Exercise::active()
            ->distinct()
            ->whereNotNull('equipment')
            ->pluck('equipment')
            ->sort()
            ->values();

        return response()->json([
            'success' => true,
            'data' => $equipmentTypes,
        ]);
    }

    /**
     * Store a newly created exercise (admin only).
     */
    public function store(Request $request): JsonResponse
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

        $validated['is_active'] = $request->has('is_active') ? true : ($request->input('is_active', true));

        $exercise = Exercise::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Exercise created successfully',
            'data' => $exercise,
        ], 201);
    }

    /**
     * Update the specified exercise (admin only).
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $exercise = Exercise::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'muscle_group' => 'nullable|string|max:255',
            'equipment' => 'nullable|string|max:255',
            'difficulty' => 'sometimes|required|in:beginner,intermediate,advanced',
            'instructions' => 'nullable|string',
            'video_urls' => 'nullable|array',
            'video_urls.*' => 'url',
            'images' => 'nullable|array',
            'images.*' => 'url',
            'is_active' => 'boolean',
        ]);

        if ($request->has('is_active')) {
            $validated['is_active'] = $request->input('is_active', true);
        }

        $exercise->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Exercise updated successfully',
            'data' => $exercise,
        ]);
    }

    /**
     * Remove the specified exercise (admin only).
     */
    public function destroy(string $id): JsonResponse
    {
        $exercise = Exercise::findOrFail($id);
        $exercise->delete();

        return response()->json([
            'success' => true,
            'message' => 'Exercise deleted successfully',
        ]);
    }
}






