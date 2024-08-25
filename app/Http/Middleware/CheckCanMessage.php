<?php

namespace App\Http\Middleware;

use App\Services\PrivacySettings\checkingSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCanMessage
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
        $can_message = $this->getSettings($user_id)->who_can_message;

        return match ($can_message) {
            'all' => $next($request),
            'only_subscribers' => $this->checkOwner($user_id) ? $this->checkSubscribe($user_id) ? $next($request) : response()
                ->json(['success' => false, 'message' => 'No rights'], 403) : response()
                ->json(['success' => false, 'message' => 'No rights'], 403),
            'only_my_subscriptions' => $this->checkOwner($user_id) ? $this->checkSubscription($user_id) ? $next($request) : response()
                ->json(['success' => false, 'message' => 'No rights'], 403) : response()
                ->json(['success' => false, 'message' => 'No rights'], 403),
            default => $this->checkOwner($user_id) ? $next($request) : response()->json(['success' => false, 'message' => 'No rights'], 403),
        };
    }
}
