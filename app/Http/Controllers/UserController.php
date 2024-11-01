<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function store(Request $request)
    {
        try {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password, // Plain text password
            ]);

            return redirect()->back();
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function index($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->delete();
        return redirect()->back()->with('success', 'User deleted successfully');
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'profile_picture' => $request->profile_picture,
        ]);

        return redirect()->back()->with('success', 'User updated successfully');
    }

    public function table()
    {
        $users = User::all();
        return view('table', ['users' => $users]);
    }

    public function showLoginForm()
    {
        return view('Login.login')->withErrors(session()->get('errors', new \Illuminate\Support\MessageBag()));
    }

    public function login(Request $request)
    {
        Auth::check();
        $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('name', $request->name)->first();

        if ($user && $user->password === $request->password) {
            Auth::user($user);
            return redirect()->intended('/chat');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->get('query');

        // Search users by name or email
        $users = User::where('name', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->get();

        return response()->json($users);
    }

    public function updateUserStatus(Request $request) {
        $user = User::find($request->user_id);
        $user->active = (bool) $request->active;  // Ensure it's stored as a boolean
        $user->save();

        return response()->json(['status' => 'User status updated successfully']);
    }


    public function getUserStatus($id) {
        $user = User::find($id);

        return response()->json([
            'active' => (bool) $user->active  // Return the user's active status as a boolean
        ]);
    }

    public function updatePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        // Store the new picture in the "public" disk
        $path = $request->file('profile_picture')->store('profile_pictures', 'public');

        // Update the user's profile picture path
        $user->profile_picture = $path;
        $user->save();

        return back()->with('success', 'Profile picture updated successfully!');
    }

}

