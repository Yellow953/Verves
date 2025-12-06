<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscription::with(['coach', 'client']);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $subscriptions = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    public function show($id)
    {
        $subscription = Subscription::with(['coach', 'client'])->findOrFail($id);
        return view('admin.subscriptions.show', compact('subscription'));
    }

    public function create()
    {
        $coaches = User::where('type', 'coach')->orderBy('name')->get();
        $clients = User::where('type', 'client')->orderBy('name')->get();
        return view('admin.subscriptions.create', compact('coaches', 'clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:users,id',
            'coach_id' => 'required|exists:users,id',
            'plan_name' => 'required|string|max:255',
            'plan_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'billing_cycle' => 'required|in:weekly,monthly,quarterly,annual',
            'sessions_included' => 'nullable|integer|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'next_billing_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,cancelled,expired,pending',
            'features' => 'nullable|array',
        ]);

        // Verify client and coach types
        $client = User::findOrFail($validated['client_id']);
        $coach = User::findOrFail($validated['coach_id']);
        
        if ($client->type !== 'client') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Selected user must be a client.');
        }
        
        if ($coach->type !== 'coach') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Selected user must be a coach.');
        }

        // Set default currency if not provided
        if (!isset($validated['currency'])) {
            $validated['currency'] = 'USD';
        }

        // Calculate next billing date if not provided
        if (!isset($validated['next_billing_date']) && $validated['status'] === 'active') {
            $startDate = \Carbon\Carbon::parse($validated['start_date']);
            switch ($validated['billing_cycle']) {
                case 'weekly':
                    $validated['next_billing_date'] = $startDate->copy()->addWeek()->toDateString();
                    break;
                case 'monthly':
                    $validated['next_billing_date'] = $startDate->copy()->addMonth()->toDateString();
                    break;
                case 'quarterly':
                    $validated['next_billing_date'] = $startDate->copy()->addMonths(3)->toDateString();
                    break;
                case 'annual':
                    $validated['next_billing_date'] = $startDate->copy()->addYear()->toDateString();
                    break;
            }
        }

        $subscription = Subscription::create($validated);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription created successfully.');
    }
}
