<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProgressTracking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProgressTrackingController extends Controller
{
    /**
     * Display a listing of progress entries.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = ProgressTracking::with(['client:id,name,email', 'coach:id,name,email', 'program:id,name']);

        if ($user->isClient()) {
            $query->where('client_id', $user->id);
        } elseif ($user->isCoach()) {
            $query->where('coach_id', $user->id);
        }

        // Filter by program
        if ($request->has('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->where('tracking_date', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->where('tracking_date', '<=', $request->end_date);
        }

        $perPage = $request->get('per_page', 15);
        $entries = $query->orderBy('tracking_date', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $entries,
        ]);
    }

    /**
     * Store a newly created progress entry.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'client_id' => 'nullable|exists:users,id',
            'program_id' => 'nullable|exists:programs,id',
            'booking_id' => 'nullable|exists:bookings,id',
            'tracking_date' => 'required|date',
            'type' => 'required|in:measurement,photo,note,exercise_log,body_composition',
            'weight_kg' => 'nullable|numeric|min:0',
            'body_fat_percentage' => 'nullable|numeric|min:0|max:100',
            'muscle_mass_kg' => 'nullable|numeric|min:0',
            'chest_cm' => 'nullable|numeric|min:0',
            'waist_cm' => 'nullable|numeric|min:0',
            'hips_cm' => 'nullable|numeric|min:0',
            'arms_cm' => 'nullable|numeric|min:0',
            'thighs_cm' => 'nullable|numeric|min:0',
            'photos' => 'nullable|array',
            'notes' => 'nullable|string',
            'exercise_data' => 'nullable|array',
            'body_composition_data' => 'nullable|array',
        ]);

        // Set client_id based on user type
        if ($user->isClient()) {
            $validated['client_id'] = $user->id;
        } elseif ($user->isCoach()) {
            if (!isset($validated['client_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client ID is required',
                ], 422);
            }
            $validated['coach_id'] = $user->id;
        }

        // Verify coach-client relationship if both are set
        if (isset($validated['coach_id']) && isset($validated['client_id'])) {
            $relationship = \App\Models\CoachClientRelationship::where('coach_id', $validated['coach_id'])
                ->where('client_id', $validated['client_id'])
                ->where('status', 'active')
                ->first();

            if (!$relationship) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active relationship between coach and client',
                ], 403);
            }
        }

        $entry = ProgressTracking::create($validated);
        $entry->load(['client:id,name,email', 'coach:id,name,email', 'program:id,name']);

        return response()->json([
            'success' => true,
            'message' => 'Progress entry created successfully',
            'data' => $entry,
        ], 201);
    }

    /**
     * Display the specified progress entry.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $entry = ProgressTracking::with(['client:id,name,email', 'coach:id,name,email', 'program:id,name'])
            ->findOrFail($id);

        // Check access
        if (($user->isClient() && $entry->client_id !== $user->id) &&
            ($user->isCoach() && $entry->coach_id !== $user->id) &&
            !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $entry,
        ]);
    }

    /**
     * Update the specified progress entry.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $entry = ProgressTracking::findOrFail($id);

        // Check authorization
        if (($user->isClient() && $entry->client_id !== $user->id) &&
            ($user->isCoach() && $entry->coach_id !== $user->id) &&
            !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $validated = $request->validate([
            'tracking_date' => 'sometimes|date',
            'type' => 'sometimes|in:measurement,photo,note,exercise_log,body_composition',
            'weight_kg' => 'nullable|numeric|min:0',
            'body_fat_percentage' => 'nullable|numeric|min:0|max:100',
            'muscle_mass_kg' => 'nullable|numeric|min:0',
            'chest_cm' => 'nullable|numeric|min:0',
            'waist_cm' => 'nullable|numeric|min:0',
            'hips_cm' => 'nullable|numeric|min:0',
            'arms_cm' => 'nullable|numeric|min:0',
            'thighs_cm' => 'nullable|numeric|min:0',
            'photos' => 'nullable|array',
            'notes' => 'nullable|string',
            'exercise_data' => 'nullable|array',
            'body_composition_data' => 'nullable|array',
        ]);

        $entry->update($validated);
        $entry->load(['client:id,name,email', 'coach:id,name,email', 'program:id,name']);

        return response()->json([
            'success' => true,
            'message' => 'Progress entry updated successfully',
            'data' => $entry,
        ]);
    }

    /**
     * Remove the specified progress entry.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $entry = ProgressTracking::findOrFail($id);

        // Check authorization
        if (($user->isClient() && $entry->client_id !== $user->id) &&
            ($user->isCoach() && $entry->coach_id !== $user->id) &&
            !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $entry->delete();

        return response()->json([
            'success' => true,
            'message' => 'Progress entry deleted successfully',
        ]);
    }
}
