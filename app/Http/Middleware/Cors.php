<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // No client here relies on cookies/credentials across origins (the
        // mobile app authenticates via a Bearer token in the Authorization
        // header), so a wildcard origin is fine. Allow-Origin: * combined
        // with Allow-Credentials: true is invalid per the CORS spec and
        // browsers reject it, so don't send the credentials header.
        return $next($request)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE,OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Authorization, Content-Type, Access-Control-Allow-Headers, X-Requested-With, Accept, Cache-Control');
    }
}
