<?php

namespace App\Observers;

use App\Models\NotificationRepost;
use App\Models\Post;

class PostObserver
{
    /**
     * Handle the Post "created" event.
     */
    public function created(Post $post): void
    {
        if($post->repost_id != null) {
            $mainPost = Post::where('id', $post->repost_id)->first();
            if($post->user_id != $mainPost->user_id) {
                NotificationRepost::create([
                    'user_id' =>  $mainPost->user_id,
                    'post_id' => $post->id
            ]);
            }
        }
    }

    /**
     * Handle the Post "updated" event.
     */
    public function updated(Post $post): void
    {
        //
    }

    /**
     * Handle the Post "deleted" event.
     */
    public function deleted(Post $post): void
    {
        //
    }

    /**
     * Handle the Post "restored" event.
     */
    public function restored(Post $post): void
    {
        //
    }

    /**
     * Handle the Post "force deleted" event.
     */
    public function forceDeleted(Post $post): void
    {
        //
    }
}
