<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Overview page showing counts and recent users
        $stats = [
            'total' => User::count(),
            'admins' => User::where('type', 'admin')->count(),
            'coaches' => User::where('type', 'coach')->count(),
            'clients' => User::where('type', 'client')->count(),
        ];

        $recentUsers = User::latest()->take(10)->get();

        return view('admin.users.index', compact('stats', 'recentUsers'));
    }

    public function admins(Request $request)
    {
        return $this->getUsersByType($request, 'admin', 'Admins');
    }

    public function coaches(Request $request)
    {
        return $this->getUsersByType($request, 'coach', 'Coaches');
    }

    public function clients(Request $request)
    {
        return $this->getUsersByType($request, 'client', 'Clients');
    }

    private function getUsersByType(Request $request, $type, $title)
    {
        $query = User::where('type', $type);

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.users.list', compact('users', 'type', 'title'));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:255',
            'type' => 'required|in:admin,coach,client',
            'role' => 'nullable|string',
            'bio' => 'nullable|string',
            'specialization' => 'nullable|string|max:255',
        ]);

        $user->update($validated);

        $redirectRoute = match($user->type) {
            'admin' => 'admin.users.admins',
            'coach' => 'admin.users.coaches',
            'client' => 'admin.users.clients',
            default => 'admin.users.index',
        };

        return redirect()->route($redirectRoute)
            ->with('success', 'User updated successfully');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        $userType = $user->type;
        $user->delete();

        $redirectRoute = match($userType) {
            'admin' => 'admin.users.admins',
            'coach' => 'admin.users.coaches',
            'client' => 'admin.users.clients',
            default => 'admin.users.index',
        };

        return redirect()->route($redirectRoute)
            ->with('success', 'User deleted successfully');
    }
}
