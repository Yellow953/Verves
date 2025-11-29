<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Program;
use App\Models\Booking;
use App\Models\Subscription;
use App\Models\Category;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'coaches' => User::where('type', 'coach')->count(),
            'clients' => User::where('type', 'client')->count(),
            'programs' => Program::count(),
            'active_programs' => Program::where('status', 'active')->count(),
            'bookings' => Booking::count(),
            'upcoming_bookings' => Booking::where('session_date', '>', now())->whereIn('status', ['pending', 'confirmed'])->count(),
            'subscriptions' => Subscription::where('status', 'active')->count(),
            'categories' => Category::where('is_active', true)->count(),
        ];

        $recent_users = User::latest()->take(5)->get();
        $recent_bookings = Booking::with(['coach', 'client'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recent_users', 'recent_bookings'));
    }
}

