<?php

namespace App\Services\File;

use App\Models\BlockedUser;
use App\Models\Post;
use App\Models\PostFile;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait checkingPostFileAccess
{
    public function checkAccessPostFile(string $filename): bool
    {
        $post = Post::whereHas('files', function ($query) use ($filename) {
            $query->where('filename', $filename);
        })->first();

        if (!$post) {
            return false;
        }

        $user = User::where('id', $post->user_id)
            ->with('privacy')->first();

        if ($user) {
            $isBlocked = BlockedUser::where('blocked_id', Auth::id())
                ->where('user_id', $user->id)
                ->exists();

            if ($isBlocked) {
                return false;
            }

            if ($user->privacy->account_type == 'private') {
                if (Subscription::where([
                    'user_id' => $user->id,
                    'follower_id' => Auth::id()
                ])->exists()) {
                    return true;
                } else return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

}
