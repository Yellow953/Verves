<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class CoachController extends Controller
{
    /**
     * Display a listing of coaches (public).
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::where('type', 'coach')
            ->select('id', 'name', 'email', 'bio', 'specialization', 'availability', 'created_at');

        // Filter by specialization if provided
        if ($request->has('specialization')) {
            $query->where('specialization', 'like', '%' . $request->specialization . '%');
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->get('per_page', 15);
        $coaches = $query->orderBy('name', 'asc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $coaches,
        ]);
    }

    /**
     * Display the specified coach (public).
     */
    public function show(string $id): JsonResponse
    {
        $coach = User::where('type', 'coach')
            ->where('id', $id)
            ->select('id', 'name', 'email', 'bio', 'specialization', 'availability', 'created_at')
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $coach,
        ]);
    }

    /**
     * Get available time slots for a coach (public).
     */
    public function availableSlots(Request $request, string $id): JsonResponse
    {
        $coach = User::where('type', 'coach')
            ->where('id', $id)
            ->firstOrFail();

        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $selectedDate = Carbon::parse($date);

        // Get coach's availability from their profile
        $availability = $coach->availability ?? [];
        
        // Get existing bookings for the selected date
        $existingBookings = Booking::where('coach_id', $coach->id)
            ->whereDate('session_date', $selectedDate)
            ->whereNotIn('status', ['cancelled'])
            ->get()
            ->map(function ($booking) {
                return [
                    'start' => Carbon::parse($booking->session_date)->format('H:i'),
                    'end' => Carbon::parse($booking->session_date)
                        ->addMinutes($booking->duration_minutes ?? 60)
                        ->format('H:i'),
                ];
            })
            ->toArray();

        // Generate available time slots based on coach's availability
        $availableSlots = [];
        
        // Default availability if not set (9 AM to 6 PM)
        $defaultHours = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'];
        
        if (empty($availability)) {
            // Use default hours
            $dayName = $selectedDate->format('D');
            $slots = $defaultHours;
        } else {
            // Get availability for the selected day
            $dayName = $selectedDate->format('D');
            $dayAvailability = $availability[$dayName] ?? null;
            
            if ($dayAvailability) {
                // Parse availability string like "9-17" or "09:00-17:00"
                if (is_string($dayAvailability)) {
                    $parts = explode('-', $dayAvailability);
                    if (count($parts) === 2) {
                        $startHour = (int) trim($parts[0]);
                        $endHour = (int) trim($parts[1]);
                        $slots = [];
                        for ($hour = $startHour; $hour < $endHour; $hour++) {
                            $slots[] = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';
                        }
                    } else {
                        $slots = $defaultHours;
                    }
                } else {
                    $slots = $defaultHours;
                }
            } else {
                $slots = [];
            }
        }

        // Filter out slots that conflict with existing bookings
        foreach ($slots as $slot) {
            $slotTime = Carbon::parse($date . ' ' . $slot);
            $slotEnd = $slotTime->copy()->addHour();
            
            $hasConflict = false;
            foreach ($existingBookings as $booking) {
                $bookingStart = Carbon::parse($date . ' ' . $booking['start']);
                $bookingEnd = Carbon::parse($date . ' ' . $booking['end']);
                
                if ($slotTime->lt($bookingEnd) && $slotEnd->gt($bookingStart)) {
                    $hasConflict = true;
                    break;
                }
            }
            
            // Only include future slots
            if (!$hasConflict && $slotTime->isFuture()) {
                $availableSlots[] = [
                    'time' => $slot,
                    'datetime' => $slotTime->toDateTimeString(),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'date' => $date,
                'coach_id' => $coach->id,
                'coach_name' => $coach->name,
                'available_slots' => $availableSlots,
            ],
        ]);
    }
}






