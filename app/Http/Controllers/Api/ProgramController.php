<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\ProgramExercise;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProgramController extends Controller
{
    /**
     * Display a listing of programs.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Program::with(['coach:id,name,email', 'client:id,name,email', 'exercises']);

        if ($user->isCoach()) {
            $query->where('coach_id', $user->id);
        } elseif ($user->isClient()) {
            $query->where('client_id', $user->id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $perPage = $request->get('per_page', 15);
        $programs = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $programs,
        ]);
    }

    /**
     * Store a newly created program.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->isCoach()) {
            return response()->json([
                'success' => false,
                'message' => 'Only coaches can create programs',
            ], 403);
        }

        $validated = $request->validate([
            'client_id' => 'nullable|exists:users,id',
            'relationship_id' => 'nullable|exists:coach_client_relationships,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'nullable|in:strength,cardio,flexibility,weight_loss,muscle_gain,rehabilitation,custom',
            'duration_weeks' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|in:draft,active,completed,archived',
            'goals' => 'nullable|array',
            'notes' => 'nullable|array',
        ]);

        $validated['coach_id'] = $user->id;

        // If client_id is provided, verify relationship
        if (isset($validated['client_id'])) {
            $relationship = \App\Models\CoachClientRelationship::where('coach_id', $user->id)
                ->where('client_id', $validated['client_id'])
                ->where('status', 'active')
                ->first();

            if (!$relationship) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active relationship with this client',
                ], 403);
            }

            $validated['relationship_id'] = $relationship->id;
        }

        $program = Program::create($validated);
        $program->load(['coach:id,name,email', 'client:id,name,email']);

        return response()->json([
            'success' => true,
            'message' => 'Program created successfully',
            'data' => $program,
        ], 201);
    }

    /**
     * Display the specified program.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $program = Program::with(['coach:id,name,email', 'client:id,name,email', 'exercises'])
            ->findOrFail($id);

        // Check access
        if ($user->isCoach() && $program->coach_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        if ($user->isClient() && $program->client_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $program,
        ]);
    }

    /**
     * Update the specified program.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $program = Program::findOrFail($id);

        if ($program->coach_id !== $user->id && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this program',
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'sometimes|in:strength,cardio,flexibility,weight_loss,muscle_gain,rehabilitation,custom',
            'duration_weeks' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'sometimes|in:draft,active,completed,archived',
            'goals' => 'nullable|array',
            'notes' => 'nullable|array',
        ]);

        $program->update($validated);
        $program->load(['coach:id,name,email', 'client:id,name,email', 'exercises']);

        return response()->json([
            'success' => true,
            'message' => 'Program updated successfully',
            'data' => $program,
        ]);
    }

    /**
     * Remove the specified program.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $program = Program::findOrFail($id);

        if ($program->coach_id !== $user->id && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this program',
            ], 403);
        }

        $program->delete();

        return response()->json([
            'success' => true,
            'message' => 'Program deleted successfully',
        ]);
    }
}
