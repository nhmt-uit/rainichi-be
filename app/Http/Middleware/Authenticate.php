<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        // This app is API-only and has no 'login' web route to redirect to;
        // returning null makes the exception handler respond with a plain
        // 401 JSON error instead of crashing on route('login').
        return null;
    }
}
