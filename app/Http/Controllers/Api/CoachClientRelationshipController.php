<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CoachClientRelationship;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CoachClientRelationshipController extends Controller
{
    /**
     * Display a listing of relationships.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = CoachClientRelationship::with(['coach:id,name,email,type', 'client:id,name,email,type']);

        if ($user->isCoach()) {
            $query->where('coach_id', $user->id);
        } elseif ($user->isClient()) {
            $query->where('client_id', $user->id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 15);
        $relationships = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $relationships,
        ]);
    }

    /**
     * Store a newly created relationship (request).
     * Can create a new client user or use existing client_id.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        // Check if creating a new client or using existing
        $createNewClient = $request->has('create_new_client') && $request->create_new_client === true;

        if ($createNewClient) {
            // Validate new client data
            $validated = $request->validate([
                'coach_id' => 'required|exists:users,id',
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'notes' => 'nullable|string', // Medical case / description
                'password' => 'nullable|string|min:8', // Optional, will generate if not provided
            ]);

            // Verify the coach_id matches the authenticated user
            if ($validated['coach_id'] != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            // Generate password if not provided
            $password = $validated['password'] ?? \Illuminate\Support\Str::random(12);

            // Create new client user
            $client = \App\Models\User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'password' => \Illuminate\Support\Facades\Hash::make($password),
                'type' => 'client',
                'role' => 'user',
            ]);

            $validated['client_id'] = $client->id;
        } else {
            // Use existing client
            $validated = $request->validate([
                'coach_id' => 'required|exists:users,id',
                'client_id' => 'required|exists:users,id',
                'notes' => 'nullable|string',
            ]);
        }

        // Set client_id based on user type (for backward compatibility)
        if ($user->isClient()) {
            $validated['client_id'] = $user->id;
        } elseif ($user->isCoach()) {
            if (!isset($validated['client_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client ID is required',
                ], 422);
            }
            // Verify the coach_id matches the authenticated user
            if ($validated['coach_id'] != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }
        }

        // Check if relationship already exists
        $existing = CoachClientRelationship::where('coach_id', $validated['coach_id'])
            ->where('client_id', $validated['client_id'])
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Relationship already exists',
                'data' => $existing,
            ], 409);
        }

        // Verify coach is actually a coach
        $coach = \App\Models\User::findOrFail($validated['coach_id']);
        if (!$coach->isCoach()) {
            return response()->json([
                'success' => false,
                'message' => 'User is not a coach',
            ], 422);
        }

        $validated['status'] = 'active'; // Auto-activate when coach creates client
        $validated['start_date'] = now()->toDateString();

        $relationship = CoachClientRelationship::create($validated);
        $relationship->load(['coach:id,name,email,type', 'client:id,name,email,type,phone,address']);

        $response = [
            'success' => true,
            'message' => $createNewClient ? 'Client created and relationship established successfully' : 'Relationship request created successfully',
            'data' => $relationship,
        ];

        // Include generated password if new client was created
        if ($createNewClient && !$request->has('password')) {
            $response['generated_password'] = $password;
            $response['message'] .= '. Please save the generated password for the client.';
        }

        return response()->json($response, 201);
    }

    /**
     * Update the specified relationship (accept/reject/pause).
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $relationship = CoachClientRelationship::findOrFail($id);

        // Check authorization
        $canUpdate = false;
        if ($user->isCoach() && $relationship->coach_id === $user->id) {
            $canUpdate = true;
        } elseif ($user->isClient() && $relationship->client_id === $user->id) {
            // Clients can only accept/reject pending requests
            if ($relationship->status === 'pending') {
                $canUpdate = true;
            }
        } elseif ($user->isAdmin()) {
            $canUpdate = true;
        }

        if (!$canUpdate) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'sometimes|in:pending,active,paused,ended',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        // If activating, set start_date if not provided
        if (isset($validated['status']) && $validated['status'] === 'active' && $relationship->status !== 'active') {
            if (!isset($validated['start_date'])) {
                $validated['start_date'] = now()->toDateString();
            }
        }

        $relationship->update($validated);
        $relationship->load(['coach:id,name,email,type', 'client:id,name,email,type']);

        return response()->json([
            'success' => true,
            'message' => 'Relationship updated successfully',
            'data' => $relationship,
        ]);
    }

    /**
     * Display the specified relationship.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $relationship = CoachClientRelationship::with(['coach:id,name,email,type', 'client:id,name,email,type'])
            ->findOrFail($id);

        // Check access
        if (($user->isCoach() && $relationship->coach_id !== $user->id) &&
            ($user->isClient() && $relationship->client_id !== $user->id) &&
            !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $relationship,
        ]);
    }

    /**
     * Remove the specified relationship.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $relationship = CoachClientRelationship::findOrFail($id);

        // Check authorization
        if (($user->isCoach() && $relationship->coach_id !== $user->id) &&
            ($user->isClient() && $relationship->client_id !== $user->id) &&
            !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $relationship->delete();

        return response()->json([
            'success' => true,
            'message' => 'Relationship deleted successfully',
        ]);
    }
}
