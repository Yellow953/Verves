<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\ProgramExercise;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProgramExerciseController extends Controller
{
    /**
     * Display exercises for a program.
     */
    public function index(Request $request, string $programId): JsonResponse
    {
        $program = Program::findOrFail($programId);
        $user = $request->user();

        // Check access
        if (($user->isCoach() && $program->coach_id !== $user->id) ||
            ($user->isClient() && $program->client_id !== $user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $exercises = $program->exercises()->get();

        return response()->json([
            'success' => true,
            'data' => $exercises,
        ]);
    }

    /**
     * Store a newly created exercise.
     */
    public function store(Request $request, string $programId): JsonResponse
    {
        $program = Program::findOrFail($programId);
        $user = $request->user();

        if ($program->coach_id !== $user->id && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to add exercises',
            ], 403);
        }

        $validated = $request->validate([
            'exercise_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'muscle_group' => 'nullable|string|max:255',
            'equipment' => 'nullable|string|max:255',
            'day_number' => 'required|integer|min:1',
            'order' => 'nullable|integer|min:0',
            'sets' => 'nullable|integer|min:1',
            'reps' => 'nullable|string|max:255',
            'weight' => 'nullable|numeric|min:0',
            'duration_seconds' => 'nullable|integer|min:1',
            'rest_seconds' => 'nullable|integer|min:0',
            'instructions' => 'nullable|string',
            'video_urls' => 'nullable|array',
            'images' => 'nullable|array',
        ]);

        $validated['program_id'] = $program->id;
        $exercise = ProgramExercise::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Exercise added successfully',
            'data' => $exercise,
        ], 201);
    }

    /**
     * Update the specified exercise.
     */
    public function update(Request $request, string $programId, string $id): JsonResponse
    {
        $program = Program::findOrFail($programId);
        $exercise = ProgramExercise::findOrFail($id);
        $user = $request->user();

        if ($exercise->program_id !== $program->id) {
            return response()->json([
                'success' => false,
                'message' => 'Exercise does not belong to this program',
            ], 404);
        }

        if ($program->coach_id !== $user->id && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $validated = $request->validate([
            'exercise_name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'muscle_group' => 'nullable|string|max:255',
            'equipment' => 'nullable|string|max:255',
            'day_number' => 'sometimes|integer|min:1',
            'order' => 'nullable|integer|min:0',
            'sets' => 'nullable|integer|min:1',
            'reps' => 'nullable|string|max:255',
            'weight' => 'nullable|numeric|min:0',
            'duration_seconds' => 'nullable|integer|min:1',
            'rest_seconds' => 'nullable|integer|min:0',
            'instructions' => 'nullable|string',
            'video_urls' => 'nullable|array',
            'images' => 'nullable|array',
        ]);

        $exercise->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Exercise updated successfully',
            'data' => $exercise,
        ]);
    }

    /**
     * Remove the specified exercise.
     */
    public function destroy(Request $request, string $programId, string $id): JsonResponse
    {
        $program = Program::findOrFail($programId);
        $exercise = ProgramExercise::findOrFail($id);
        $user = $request->user();

        if ($exercise->program_id !== $program->id) {
            return response()->json([
                'success' => false,
                'message' => 'Exercise does not belong to this program',
            ], 404);
        }

        if ($program->coach_id !== $user->id && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $exercise->delete();

        return response()->json([
            'success' => true,
            'message' => 'Exercise deleted successfully',
        ]);
    }
}
