<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    // GET /admin/users
    public function index(Request $request): View
    {
        $query = User::where('role', 'user');

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $users = $query->withCount(['accounts', 'transactions'])
            ->with(['activeSubscription.plan'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $totalUsers  = User::where('role', 'user')->count();
        $activeUsers = User::where('role', 'user')->where('status', 'active')->count();
        $bannedUsers = User::where('role', 'user')->where('status', 'banned')->count();

        return view('admin.users.index', compact(
            'users',
            'search',
            'status',
            'totalUsers',
            'activeUsers',
            'bannedUsers'
        ));
    }

    // GET /admin/users/{user}
    public function show(User $user): View
    {
        $user->load(['activeSubscription.plan']);

        return view('admin.users.show', compact('user'));
    }

    // PUT /admin/users/{user}
    public function update(Request $request, User $user): RedirectResponse
    {
        if ($user->role === 'admin') {
            return back()->withErrors(['action' => 'Tidak bisa mengedit akun admin.']);
        }

        $data = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'email'  => ['required', 'email', 'unique:users,email,' . $user->id],
            'status' => ['required', 'in:active,banned'],
        ]);

        $user->update($data);

        return back()->with('success', "User {$user->name} berhasil diperbarui.");
    }

    // PATCH /admin/users/{user}/ban
    public function ban(User $user): RedirectResponse
    {
        if ($user->role === 'admin') {
            return back()->withErrors(['action' => 'Tidak bisa ban akun admin.']);
        }

        $user->update(['status' => 'banned']);

        return back()->with('success', "User {$user->name} berhasil di-ban.");
    }

    // PATCH /admin/users/{user}/unban
    public function unban(User $user): RedirectResponse
    {
        $user->update(['status' => 'active']);

        return back()->with('success', "User {$user->name} berhasil diaktifkan kembali.");
    }
}
