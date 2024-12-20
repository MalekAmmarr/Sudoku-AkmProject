<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        // Check if the request is for an API route
        if ($request->expectsJson() || $request->is('api/*')) {
            // If the route is '/api/users/retrieve', return null
            if ($request->is('api/users/retrieve')) {
                return null; // No redirection for this route
            }

            return response()->json(['message' => 'Unauthenticated'], 401); // Default JSON response
        }

        // Redirect other unauthenticated requests to the 'login' route
        return route('login');
    }
}
