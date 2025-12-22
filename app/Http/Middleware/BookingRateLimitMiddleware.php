<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class BookingRateLimitMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $type = 'general'): Response
    {
        $limits = config('booking.rate_limiting');
        $limit = $limits[$type] ?? $limits['general_api'];
        
        [$maxAttempts, $decayMinutes] = explode(',', $limit);
        
        $key = $this->resolveRequestSignature($request, $type);
        
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return response()->json([
                'success' => false,
                'message' => 'Too many requests. Please try again later.',
                'error_type' => 'rate_limit_exceeded',
                'retry_after' => RateLimiter::availableIn($key)
            ], 429);
        }
        
        RateLimiter::hit($key, $decayMinutes * 60);
        
        $response = $next($request);
        
        // Add rate limit headers
        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', RateLimiter::remaining($key, $maxAttempts));
        $response->headers->set('X-RateLimit-Reset', RateLimiter::availableIn($key));
        
        return $response;
    }
    
    /**
     * Resolve the rate limiting key for the request.
     */
    protected function resolveRequestSignature(Request $request, string $type): string
    {
        $userId = $request->user()?->id ?? 'guest';
        $ip = $request->ip();
        
        return "booking_rate_limit:{$type}:{$userId}:{$ip}";
    }
}
