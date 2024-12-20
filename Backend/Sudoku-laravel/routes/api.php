<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Orion\Facades\Orion;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TokenController;
use Illuminate\Support\Facades\Auth;

// Custom routes for login and adding scores
// User Routes
Route::prefix('users')->group(function () {
    Route::post('/login', [UserController::class, 'login']); // Login user
    Route::post('/register', [UserController::class, 'store']); // Register user
    //Route::get('/', [UserController::class, 'index']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    

    Route::middleware('auth:sanctum')->group(function () {
        // Profile management
        //Route::post('/get-token', [UserController::class, 'getToken']);
         // Get all users
        Route::get('/retrieve', [UserController::class, 'retrieveProfile']); // Retrieve profile
        Route::put('/update-profile', [UserController::class, 'updateProfile']); // Update profile
        Route::delete('/delete-Profile', [UserController::class, 'deleteProfile']); // Delete profile

        // Score management
        Route::post('/addScore', [UserController::class, 'addScore']); // Add a user's score
        Route::get('/scores/top', [UserController::class, 'topScores']); // Get top 3 scores
        Route::post('/scores/add', [UserController::class, 'autoAddScore']); // Auto add score
    });

});


// Route::middleware('auth:sanctum')->get('/user/token', function () {
//     return response()->json(['token' => Auth::user()->currentAccessToken()->plainTextToken]);
// });



