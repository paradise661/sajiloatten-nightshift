<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIfActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->status !== 'Active') {
            if ($request->user()?->device_ids) {
                $request->user()->update([
                    'device_ids' => null
                ]);
            }
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
}
