<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AddUser extends Controller
{
    // Display the Add User form
    public function showAddUserForm()
    {
        return view('addUser');
    }

    // Handle the form submission
    public function UserAdd(Request $request)
{
    // Validate the input
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8',
        'score' => 'nullable|integer|min:0',
        'card_number' => 'required|string|size:16',
        'card_brand' => 'required|string|max:50',
        'card_expires_at' => 'required|date',
    ]);

    // Log the validated data to check
    \Log::info('Validated data:', $validatedData);

    try {
        DB::table('users')->insert([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'score' => $validatedData['score'] ?? 0,
            'payment_gateway_customer_id' => null,
            'card_number' => $validatedData['card_number'],
            'card_brand' => $validatedData['card_brand'],
            'card_expires_at' => $validatedData['card_expires_at'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    } catch (\Exception $e) {
        \Log::error('Error inserting user: ' . $e->getMessage());
        return redirect()->back()->withErrors('Failed to add user.');
    };

    return redirect()->back()->with('success', 'User added successfully!');
}

}
