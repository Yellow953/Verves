<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of subscriptions.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Subscription::with(['client:id,name,email', 'coach:id,name,email']);

        if ($user->isClient()) {
            $query->where('client_id', $user->id);
        } elseif ($user->isCoach()) {
            $query->where('coach_id', $user->id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 15);
        $subscriptions = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $subscriptions,
        ]);
    }

    /**
     * Store a newly created subscription.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'coach_id' => 'required|exists:users,id',
            'client_id' => 'nullable|exists:users,id',
            'plan_name' => 'required|string|max:255',
            'plan_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'billing_cycle' => 'required|in:weekly,monthly,quarterly,annual',
            'sessions_included' => 'nullable|integer|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'features' => 'nullable|array',
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
            // Verify the coach_id matches the authenticated user
            if ($validated['coach_id'] !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }
        }

        // Verify coach-client relationship
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

        $validated['status'] = 'pending';
        $validated['currency'] = $validated['currency'] ?? 'USD';
        $subscription = Subscription::create($validated);
        $subscription->load(['client:id,name,email', 'coach:id,name,email']);

        return response()->json([
            'success' => true,
            'message' => 'Subscription created successfully',
            'data' => $subscription,
        ], 201);
    }

    /**
     * Display the specified subscription.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $subscription = Subscription::with(['client:id,name,email', 'coach:id,name,email'])
            ->findOrFail($id);

        // Check access
        if (($user->isClient() && $subscription->client_id !== $user->id) &&
            ($user->isCoach() && $subscription->coach_id !== $user->id) &&
            !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $subscription,
        ]);
    }

    /**
     * Update the specified subscription.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $subscription = Subscription::findOrFail($id);

        // Only coach or admin can update
        if ($subscription->coach_id !== $user->id && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'sometimes|in:active,cancelled,expired,pending',
            'end_date' => 'nullable|date',
            'next_billing_date' => 'nullable|date',
            'cancellation_reason' => 'nullable|string',
        ]);

        // If cancelling, set cancelled_at
        if (isset($validated['status']) && $validated['status'] === 'cancelled' && $subscription->status !== 'cancelled') {
            $validated['cancelled_at'] = now();
        }

        $subscription->update($validated);
        $subscription->load(['client:id,name,email', 'coach:id,name,email']);

        return response()->json([
            'success' => true,
            'message' => 'Subscription updated successfully',
            'data' => $subscription,
        ]);
    }

    /**
     * Cancel a subscription.
     */
    public function cancel(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $subscription = Subscription::findOrFail($id);

        // Client or coach can cancel
        if (($user->isClient() && $subscription->client_id !== $user->id) &&
            ($user->isCoach() && $subscription->coach_id !== $user->id) &&
            !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        if ($subscription->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Subscription is already cancelled',
            ], 400);
        }

        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $request->input('reason'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subscription cancelled successfully',
            'data' => $subscription,
        ]);
    }
}
