<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->orderBy('username')
            ->paginate(15);

        return view('users.index', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        $roles = DB::table('user_roles')->orderBy('role_name')->get();

        return view('users.create', ['roles' => $roles]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:255', 'regex:/^[0-9+\-\s()]*$/'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'integer', 'exists:user_roles,id'],
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::query()->create([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => $validated['role_id'],
                'is_active' => true,
            ]);

            $roleName = DB::table('user_roles')->where('id', $validated['role_id'])->value('role_name');

            DB::table('health_workers')->insert([
                'user_id' => $user->id,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'role' => $roleName,
                'contact_number' => $validated['contact_number'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()
            ->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = DB::table('user_roles')->orderBy('role_name')->get();
        $healthWorker = DB::table('health_workers')->where('user_id', $user->id)->first();

        return view('users.edit', [
            'user' => $user,
            'roles' => $roles,
            'healthWorker' => $healthWorker,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:255', 'regex:/^[0-9+\-\s()]*$/'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,'.$user->id],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'integer', 'exists:user_roles,id'],
        ]);

        DB::transaction(function () use ($user, $validated) {
            if ($user->role_id !== $validated['role_id']) {
                $user->role_id = $validated['role_id'];
            }

            if ($user->username !== $validated['username']) {
                $user->username = $validated['username'];
            }

            if ($user->email !== $validated['email']) {
                $user->email = $validated['email'];
            }

            if (! empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();

            $roleName = DB::table('user_roles')->where('id', $validated['role_id'])->value('role_name');

            DB::table('health_workers')
                ->where('user_id', $user->id)
                ->update([
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'role' => $roleName,
                    'contact_number' => $validated['contact_number'] ?? null,
                    'updated_at' => now(),
                ]);
        });

        return redirect()
            ->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function disable(User $user)
    {
        if (! $user->is_active) {
            return redirect()
                ->route('users.index')
                ->with('success', 'User is already disabled.');
        }

        $user->is_active = false;
        $user->save();

        return redirect()
            ->route('users.index')
            ->with('success', 'User disabled successfully.');
    }

    public function enable(Request $request, User $user)
    {
        // Validate password confirmation
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        if ($user->is_active) {
            return redirect()
                ->route('users.index')
                ->with('success', 'User is already enabled.');
        }

        $user->is_active = true;
        $user->save();

        return redirect()
            ->route('users.index')
            ->with('success', 'User enabled successfully.');
    }

    public function destroy(Request $request, User $user)
    {
        // Only admins can delete users
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        // Validate password confirmation
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        // Delete the user (this will cascade delete health_worker due to foreign key constraint)
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function passwordResetRequests()
    {
        $requests = \App\Models\PasswordResetRequest::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('users.password-reset-requests', [
            'requests' => $requests,
        ]);
    }

    public function completePasswordResetRequest(Request $request, \App\Models\PasswordResetRequest $passwordResetRequest)
    {
        $passwordResetRequest->update([
            'status' => 'completed',
            'admin_note' => $request->input('admin_note'),
            'completed_at' => now(),
        ]);

        return redirect()->route('users.password-reset-requests')->with('success', 'Password reset request marked as completed.');
    }
}
