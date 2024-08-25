<?php

namespace App\Http\Middleware;

use App\Models\PrivacySettings;
use App\Models\Subscription;
use App\Services\PrivacySettings\checkingSettings;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountType
{
    use checkingSettings;

    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user_id = $request->input('user_id');
        $account_type = $this->getSettings($user_id)->account_type;
        if ($account_type === 'public') {
            return $next($request);
        } else {
            if ($this->checkOwner($user_id)) {
                return $next($request);
            } else {
                return $this->checkSubscribe($user_id) ? $next($request) : response()
                    ->json(['success' => false, 'message' => 'No rights'], 403);
            }
        }
    }
}
