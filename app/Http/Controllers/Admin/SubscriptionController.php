<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
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
}
