<?php

namespace App\Observers;

use App\Models\Comment;
use App\Models\NotificationComment;
use App\Models\NotificationCommentReply;
use App\Models\Post;

class CommentObserver
{
    /**
     * Handle the PostComment "created" event.
     */
    public function created(Comment $comment): void
    {
        if($comment->reply_id == null) {
            $post = Post::where('id', $comment->post_id)->first();

            if ($post->user_id != $comment->user_id) {
                NotificationComment::create([
                    'comment_id' => $comment->id,
                    'user_id' => $post->user_id
                ]);
            }
        }  else {
            $mainComment = Comment::where('id', $comment->reply_id)->first();
            if ($comment->user_id != $mainComment->user_id) {
                NotificationCommentReply::create([
                    'comment_id' => $comment->id,
                    'reply_id' => $comment->reply_id,
                    'user_id' => $mainComment->user_id
                ]);
            }
        }
    }

    /**
     * Handle the PostComment "updated" event.
     */
    public function updated(Comment $comment): void
    {
        //
    }

    /**
     * Handle the PostComment "deleted" event.
     */
    public function deleted(Comment $comment): void
    {
        //
    }

    /**
     * Handle the PostComment "restored" event.
     */
    public function restored(Comment $comment): void
    {
        //
    }

    /**
     * Handle the PostComment "force deleted" event.
     */
    public function forceDeleted(Comment $comment): void
    {
        //
    }
}
