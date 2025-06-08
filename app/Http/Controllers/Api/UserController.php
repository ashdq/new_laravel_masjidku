<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the users (admin only).
     */
    public function index()
    {
        $users = User::all();
        return response()->json($users->makeHidden(['password']));
    }

    /**
     * Store a newly created user (admin only).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'roles' => ['required', Rule::in(['admin', 'takmir', 'warga'])]
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'roles' => $validated['roles']
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user->makeHidden(['password'])
        ], 201);
    }

    /**
     * Display the specified user (admin only).
     */
    public function show(User $user)
    {
        return response()->json($user->makeHidden(['password']));
    }

    /**
     * Update the specified user (admin only).
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'password' => 'sometimes|string|min:8|nullable',
            'roles' => ['sometimes', Rule::in(['admin', 'takmir', 'warga'])]
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user->fresh()->makeHidden(['password'])
        ]);
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|string|min:8',
        ]);

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['message' => 'Password lama salah'], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password berhasil diubah']);
    }


    /**
     * Remove the specified user (admin only).
     */
    public function destroy(User $user)
    {
        // Prevent admin from deleting themselves
        if (auth()->id() === $user->id) {
            return response()->json(['message' => 'You cannot delete your own account'], 403);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted successfully'], 204);
    }

    /**
     * Allow users to delete their own account.
     */
    public function deleteOwnAccount(Request $request)
    {
        $user = $request->user();
        
        // Optional: Add password confirmation
        if ($request->has('password')) {
            if (!Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Password is incorrect'], 403);
            }
        }

        $user->delete();
        return response()->json(['message' => 'Account deleted successfully'], 204);
    }
}