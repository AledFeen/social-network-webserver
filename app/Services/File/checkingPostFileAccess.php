<?php

namespace App\Services\File;

use App\Models\BlockedUser;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Post;
use App\Models\PostFile;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserChatLink;
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

        if($user->id === Auth::id()) {
            return true;
        }

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
                } else { return false; }
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    public function checkAccessMessageFile(string $filename): bool
    {
        $message = Message::whereHas('files', function ($query) use ($filename) {
            $query->where('filename', $filename);
        })->first();

        $link = UserChatLink::where('id', $message->link_id)->first();

        return (bool)UserChatLink::where('chat_id', $link->chat_id)
            ->where('user_id', Auth::id())->first();
    }

}
