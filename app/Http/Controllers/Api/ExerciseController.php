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
}

