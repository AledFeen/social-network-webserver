<?php

namespace App\Http\Middleware;

use App\Models\BannedUser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckBanned
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (BannedUser::where('user_id', Auth::id())->exists()) {
            return response()->json(['success' => false, 'message' => 'Your account has been blocked'], 403);
        }

        return $next($request);
    }
}
