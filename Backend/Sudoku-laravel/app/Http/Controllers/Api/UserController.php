<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Score;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // Return all users as JSON
        $users = User::all();
        return response()->json($users, 200);
    }

    public function login(Request $request)
{
    $validated = $request->validate([
        'username' => 'required|string',
        'password' => 'required|string',
    ]);

    $user = User::where('username', $validated['username'])->first();

    if (!$user || !Hash::check($validated['password'], $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    // Create the token
    $token = $user->createToken('auth_token')->plainTextToken;

    // Store the token in an HTTP-only cookie
    return response()->json([
        'message' => 'Login successful',
        'user' => $user, 'token' => $token // Send user info if needed
    ], 200)->cookie(
        'auth_token', // Cookie name
        $token,       // Cookie value
        60 * 24 * 7,  // Expiry in minutes (7 days)
        '/',          // Path
        null,         // Domain (null for the current domain)
        true,         // Secure (true to only send on HTTPS)
        true          // HttpOnly (true to prevent JS access)
    );
}

public function getToken(Request $request)
    {
        if (Auth::check()) {
            $token = Auth::user()->currentAccessToken()->plainTextToken;
            return response()->json(['token' => $token]);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }

    
    public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();

    return response()->json(['message' => 'Logged out successfully'], 200);
}


    // public function profile(Request $request)
    // {
    //     $user = $request->user();

    //     return response()->json([
    //         'username' => $user->username,
    //         'email' => $user->email,
    //     ]);
    // }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|min:3|max:255|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'preferences' => 'nullable|string',
            'creditCard' => 'nullable|string|regex:/^\d{4}-\d{4}-\d{4}-\d{4}$/',
            'expiryDate' => 'nullable|string|size:5|regex:/^(0[1-9]|1[0-2])\/\d{2}$/',
            'cvv' => 'nullable|string|size:3|regex:/^\d{3}$/',
        ]);

        $profilePicturePath = $request->hasFile('profile_picture')
            ? $request->file('profile_picture')->store('profile_pictures', 'public')
            : null;

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'profile_picture' => $profilePicturePath,
            'preferences' => $validated['preferences'] ?? null,
            'credit_card' => $validated['creditCard'] ?? null,
            'expiry_date' => $validated['expiryDate'] ?? null,
            'cvv' => $validated['cvv'] ?? null,
        ]);

        return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
    }

    public function addScore(Request $request)
{
    $validated = $request->validate([
        'score' => 'required|integer|min:0',
    ]);

    // Get the authenticated user from the token
    $user = Auth::user();

    if (!$user) {
        return response()->json(['message' => 'User not authenticated'], 401);
    }

    // Update user's score
    $user->score = ($user->score ?? 0) + $validated['score'];
    $user->save();

    // Save the score in the 'scores' table
    Score::create([
        'user_id' => $user->id,
        'score' => $validated['score'],
        'name' => $user->name,
        'username' => $user->username,
    ]);

    return response()->json(['message' => 'Score added successfully!', 'user' => $user], 200);
}

    public function autoAddScore(Request $request, $userId)
    {
        $validated = $request->validate([
            'score' => 'required|integer|min:0',
        ]);

        $user = User::findOrFail($userId);

        $newScore = $validated['score'];

        // Check and insert if it's a new high score
        if ($newScore > $user->score) {
            $user->updateHighScore($newScore);
        } else {
            $user->scores()->create([
                'user_id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'score' => $newScore,
            ]);
        }

        return response()->json(['message' => 'Score added successfully!', 'user' => $user], 200);
    }

    public function topScores()
    {
        $topScores = Score::orderBy('score', 'desc')->take(3)->get();
        

        return response()->json($topScores, 200);
    }

    public function retrieveProfile()
{
    // Get the authenticated user
    $user = Auth::user();

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    return response()->json($user, 200);
}

public function updateProfile(Request $request)
{
    // Get the authenticated user
    $user = Auth::user();

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $validated = $request->validate([
        'name' => 'string|max:255',
        'username' => 'string|min:3|max:255|unique:users,username,' . $user->id,
        'email' => 'email|unique:users,email,' . $user->id,
        'password' => 'string|min:8',
        'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'preferences' => 'nullable|string',
    ]);

    if ($request->hasFile('profile_picture')) {
        $user->profile_picture = $request->file('profile_picture')->store('profile_pictures', 'public');
    }

    $user->update(array_merge($validated, [
        'password' => $request->filled('password') ? Hash::make($validated['password']) : $user->password,
    ]));

    return response()->json(['message' => 'User profile updated successfully', 'user' => $user], 200);
}

public function deleteProfile()
{
    // Get the authenticated user
    $user = Auth::user();

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $user->delete();

    return response()->json(['message' => 'User profile deleted successfully'], 200);
}
}
