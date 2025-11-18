<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientHealthData;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClientHealthDataController extends Controller
{
    /**
     * Display health data for a client.
     */
    public function show(Request $request, string $clientId): JsonResponse
    {
        $user = $request->user();

        // Only client, their coach, or admin can view
        if ($user->isClient() && $user->id != $clientId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        if ($user->isCoach()) {
            // Verify relationship
            $relationship = \App\Models\CoachClientRelationship::where('coach_id', $user->id)
                ->where('client_id', $clientId)
                ->where('status', 'active')
                ->first();

            if (!$relationship) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active relationship with this client',
                ], 403);
            }
        }

        $healthData = ClientHealthData::where('client_id', $clientId)->first();

        if (!$healthData) {
            return response()->json([
                'success' => false,
                'message' => 'Health data not found',
            ], 404);
        }

        // Check consent
        if (!$healthData->hasConsent() && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Client has not given consent to share health data',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $healthData,
        ]);
    }

    /**
     * Store or update health data.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->isClient()) {
            return response()->json([
                'success' => false,
                'message' => 'Only clients can create health data',
            ], 403);
        }

        $validated = $request->validate([
            'consent_given' => 'required|boolean',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other,prefer_not_to_say',
            'height_cm' => 'nullable|numeric|min:0',
            'medical_conditions' => 'nullable|array',
            'medications' => 'nullable|array',
            'injuries' => 'nullable|array',
            'allergies' => 'nullable|array',
            'fitness_goals' => 'nullable|string',
            'previous_experience' => 'nullable|string',
            'activity_level' => 'nullable|in:sedentary,lightly_active,moderately_active,very_active,extremely_active',
            'dietary_restrictions' => 'nullable|string',
            'lifestyle_notes' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:255',
            'emergency_contact_relationship' => 'nullable|string|max:255',
        ]);

        $validated['client_id'] = $user->id;

        // Set consent date if consent is given
        if ($validated['consent_given']) {
            $validated['consent_date'] = now();
        }

        $healthData = ClientHealthData::updateOrCreate(
            ['client_id' => $user->id],
            $validated
        );

        return response()->json([
            'success' => true,
            'message' => 'Health data saved successfully',
            'data' => $healthData,
        ], 201);
    }

    /**
     * Update health data.
     */
    public function update(Request $request, string $clientId): JsonResponse
    {
        $user = $request->user();

        if ($user->id != $clientId && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $healthData = ClientHealthData::where('client_id', $clientId)->firstOrFail();

        $validated = $request->validate([
            'consent_given' => 'sometimes|boolean',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other,prefer_not_to_say',
            'height_cm' => 'nullable|numeric|min:0',
            'medical_conditions' => 'nullable|array',
            'medications' => 'nullable|array',
            'injuries' => 'nullable|array',
            'allergies' => 'nullable|array',
            'fitness_goals' => 'nullable|string',
            'previous_experience' => 'nullable|string',
            'activity_level' => 'nullable|in:sedentary,lightly_active,moderately_active,very_active,extremely_active',
            'dietary_restrictions' => 'nullable|string',
            'lifestyle_notes' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:255',
            'emergency_contact_relationship' => 'nullable|string|max:255',
        ]);

        // Handle consent withdrawal
        if (isset($validated['consent_given']) && !$validated['consent_given'] && $healthData->consent_given) {
            $validated['consent_withdrawn_at'] = now();
        }

        // Set consent date if consent is given for the first time
        if (isset($validated['consent_given']) && $validated['consent_given'] && !$healthData->consent_given) {
            $validated['consent_date'] = now();
            $validated['consent_withdrawn_at'] = null;
        }

        $healthData->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Health data updated successfully',
            'data' => $healthData,
        ]);
    }

    /**
     * Request data deletion (GDPR).
     */
    public function requestDeletion(Request $request, string $clientId): JsonResponse
    {
        $user = $request->user();

        if ($user->id != $clientId && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $healthData = ClientHealthData::where('client_id', $clientId)->firstOrFail();
        $healthData->update([
            'data_deletion_requested' => true,
            'data_deletion_requested_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data deletion requested. Your data will be processed according to GDPR regulations.',
        ]);
    }
}
