<?php

namespace App\Observers;

use App\Models\NotificationLike;
use App\Models\Post;
use App\Models\PostLike;

class PostLikeObserver
{
    /**
     * Handle the PostLike "created" event.
     */
    public function created(PostLike $postLike): void
    {
        $post = Post::where('id', $postLike->post_id)->first();
        if ($post->user_id != $postLike->user_id) {
            NotificationLike::create([
                'user_id' => $post->user_id,
                'like_id' => $postLike->id
            ]);
        }
    }

    /**
     * Handle the PostLike "updated" event.
     */
    public function updated(PostLike $postLike): void
    {

    }

    /**
     * Handle the PostLike "deleted" event.
     */
    public function deleted(PostLike $postLike): void
    {
        //
    }

    /**
     * Handle the PostLike "restored" event.
     */
    public function restored(PostLike $postLike): void
    {
        //
    }

    /**
     * Handle the PostLike "force deleted" event.
     */
    public function forceDeleted(PostLike $postLike): void
    {
        //
    }
}
