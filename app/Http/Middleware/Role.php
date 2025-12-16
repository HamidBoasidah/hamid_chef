<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\ForbiddenException;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next , string $role): Response
    {
        // Try common guards (web + API/Sanctum). Accept whichever guard has
        // an authenticated user.
        // We support web + sanctum only (Sanctum handles SPA cookies and API tokens)
        $guards = ['web', 'sanctum'];

        $user = null;
        foreach ($guards as $g) {
            if (auth($g)->check()) {
                $user = auth($g)->user();
                break;
            }
        }

        // If no authenticated user found, respond appropriately depending on
        // whether the request expects JSON (API) or not (web).
        if (!$user) {
            // Throw standardized unauthorized exception so global handler
            // returns the unified JSON shape used across the API.
            throw new UnauthorizedException();
        }

        // If user does not have the required role, return 403 (JSON for API
        // requests, abort for web).
        if (($user->user_type ?? null) !== $role) {
            // Use the application's ForbiddenException for consistent JSON
            // error responses.
            throw new ForbiddenException();
        }

        return $next($request);
    }
}
