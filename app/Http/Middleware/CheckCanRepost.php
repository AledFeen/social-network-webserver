<?php

namespace App\Http\Middleware;

use App\Models\Post;
use App\Services\PrivacySettings\checkingSettings;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckCanRepost
{
    use checkingSettings;

    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if($request->repost_id != null) {
            $repost = Post::where('id', $request->repost_id)->first();
            $user_id = $repost->user_id;
            $can_repost = $this->getSettings($user_id)->who_can_repost;
            if ($can_repost === 'all') {
                return $next($request);
            } else if ($can_repost === 'only_subscribers') {
                if ($this->checkOwner($user_id)) {
                    return $next($request);
                } else {
                    return $this->checkSubscribe($user_id) ? $next($request) : response()
                        ->json(['success' => false, 'message' => 'No rights'], 403);
                }
            } else {
                if ($this->checkOwner($user_id)) {
                    return $next($request);
                } else {
                    return response()
                        ->json(['success' => false, 'message' => 'No rights'], 403);
                }
            }
        } else {
            return $next($request);
        }
    }
}
