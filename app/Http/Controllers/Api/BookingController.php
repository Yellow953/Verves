<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Booking::with(['coach:id,name,email', 'client:id,name,email', 'program:id,name']);

        if ($user->isCoach()) {
            $query->where('coach_id', $user->id);
        } elseif ($user->isClient()) {
            $query->where('client_id', $user->id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->where('session_date', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->where('session_date', '<=', $request->end_date);
        }

        // Filter upcoming/past
        if ($request->has('upcoming')) {
            $query->where('session_date', '>', now());
        }

        $perPage = $request->get('per_page', 15);
        $bookings = $query->orderBy('session_date', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $bookings,
        ]);
    }

    /**
     * Store a newly created booking.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'coach_id' => 'required|exists:users,id',
            'client_id' => 'nullable|exists:users,id',
            'program_id' => 'nullable|exists:programs,id',
            'session_date' => 'required|date|after:now',
            'duration_minutes' => 'nullable|integer|min:15|max:480',
            'session_type' => 'nullable|in:in_person,online,hybrid',
            'location' => 'nullable|string|max:255',
            'meeting_link' => 'nullable|url|max:255',
            'notes' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
        ]);

        // Set client_id based on user type
        if ($user->isClient()) {
            $validated['client_id'] = $user->id;
        } elseif ($user->isCoach()) {
            // Coach can book for a client
            if (!isset($validated['client_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client ID is required',
                ], 422);
            }
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

        // Check for scheduling conflicts
        $conflict = Booking::where('coach_id', $validated['coach_id'])
            ->where('session_date', '>=', $validated['session_date'])
            ->where('session_date', '<', date('Y-m-d H:i:s', strtotime($validated['session_date'] . ' + ' . ($validated['duration_minutes'] ?? 60) . ' minutes')))
            ->whereNotIn('status', ['cancelled'])
            ->exists();

        if ($conflict) {
            return response()->json([
                'success' => false,
                'message' => 'Coach has a conflicting booking at this time',
            ], 409);
        }

        $booking = Booking::create($validated);
        $booking->load(['coach:id,name,email', 'client:id,name,email', 'program:id,name']);

        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully',
            'data' => $booking,
        ], 201);
    }

    /**
     * Display the specified booking.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $booking = Booking::with(['coach:id,name,email', 'client:id,name,email', 'program:id,name'])
            ->findOrFail($id);

        // Check access
        if (($user->isCoach() && $booking->coach_id !== $user->id) ||
            ($user->isClient() && $booking->client_id !== $user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $booking,
        ]);
    }

    /**
     * Update the specified booking.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $booking = Booking::findOrFail($id);

        // Check authorization
        $canUpdate = false;
        if ($user->isCoach() && $booking->coach_id === $user->id) {
            $canUpdate = true;
        } elseif ($user->isClient() && $booking->client_id === $user->id) {
            // Clients can only update certain fields
            $canUpdate = true;
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
            'session_date' => 'sometimes|date|after:now',
            'duration_minutes' => 'nullable|integer|min:15|max:480',
            'session_type' => 'sometimes|in:in_person,online,hybrid',
            'location' => 'nullable|string|max:255',
            'meeting_link' => 'nullable|url|max:255',
            'notes' => 'nullable|string',
            'client_notes' => 'nullable|string',
            'coach_notes' => 'nullable|string',
            'status' => 'sometimes|in:pending,confirmed,completed,cancelled,no_show',
            'price' => 'nullable|numeric|min:0',
            'payment_status' => 'nullable|in:pending,paid,refunded',
        ]);

        // If cancelling, set cancelled_at
        if (isset($validated['status']) && $validated['status'] === 'cancelled' && $booking->status !== 'cancelled') {
            $validated['cancelled_at'] = now();
            if ($request->has('cancellation_reason')) {
                $validated['cancellation_reason'] = $request->cancellation_reason;
            }
        }

        $booking->update($validated);
        $booking->load(['coach:id,name,email', 'client:id,name,email', 'program:id,name']);

        return response()->json([
            'success' => true,
            'message' => 'Booking updated successfully',
            'data' => $booking,
        ]);
    }

    /**
     * Cancel a booking.
     */
    public function cancel(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $booking = Booking::findOrFail($id);

        if (!$booking->canBeCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'This booking cannot be cancelled',
            ], 403);
        }

        // Check authorization
        if (($user->isCoach() && $booking->coach_id !== $user->id) &&
            ($user->isClient() && $booking->client_id !== $user->id) &&
            !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $request->input('reason'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled successfully',
            'data' => $booking,
        ]);
    }

    /**
     * Remove the specified booking.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $booking = Booking::findOrFail($id);

        if (($user->isCoach() && $booking->coach_id !== $user->id) &&
            !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $booking->delete();

        return response()->json([
            'success' => true,
            'message' => 'Booking deleted successfully',
        ]);
    }
}
