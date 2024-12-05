<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AddUser;

// Route for the welcome page
Route::get('/', function () {
    return view('welcome');
});

// Route to show the form
Route::get('/showAddUserForm', function()
{
    return view('addUser');
});

// Route to handle form submission
Route::post('/UserAdd', [AddUser::class, 'UserAdd']);
