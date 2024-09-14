<?php

namespace App\Http\Middleware;

use App\Models\Post;
use App\Services\PrivacySettings\checkingSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCanComment
{
    use checkingSettings;

    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $post = Post::where('id', $request->post_id)->first();
        $user_id = $post->user_id;
        $can_comment = $this->getSettings($user_id)->who_can_comment;
        if ($can_comment === 'all') {
            return $next($request);
        } else if ($can_comment === 'only_subscribers') {
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
    }
}
