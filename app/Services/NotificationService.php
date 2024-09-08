<?php

namespace App\Services;

use App\Models\dto\CommentNotificationDTO;
use App\Models\dto\FollowNotificationDTO;
use App\Models\dto\LikeNotificationDTO;
use App\Models\dto\RepostNotificationDTO;
use App\Models\dto\UserDTO;
use App\Models\NotificationComment;
use App\Models\NotificationCommentReply;
use App\Models\NotificationFollow;
use App\Models\NotificationLike;
use App\Models\NotificationRepost;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    public function getFollowers()
    {
        $notifications = NotificationFollow::where('user_id', Auth::id())
            ->with('follower.account')
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->getFollowNotificationDTOs($notifications);
    }

    protected function getFollowNotificationDTOs($notifications)
    {
        return $notifications->map(function ($notification) {
           return new FollowNotificationDTO(
               $notification->id,
               new UserDTO(
                   $notification->follower->id,
                   $notification->follower->name,
                   $notification->follower->account->image
               ),
               $notification->created_at
           );
        });
    }

    public function getLikes()
    {
        $notifications = NotificationLike::where('user_id', Auth::id())
            ->with('like.user.account')
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->getLikeNotificationDTOs($notifications);
    }

    protected function getLikeNotificationDTOs($notifications)
    {
        return $notifications->map(function ($notification) {
            return new LikeNotificationDTO(
               $notification->id,
               new UserDTO(
                   $notification->like->user->id,
                   $notification->like->user->name,
                   $notification->like->user->account->image,
               ),
               $notification->like->post_id,
               $notification->created_at
            );
        });
    }

    public function getComments()
    {
        $notifications = NotificationComment::where('user_id', Auth::id())
            ->with('comment.user.account')
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->getCommentNotificationDTOs($notifications);
    }

    public function getCommentReplies()
    {
        $notifications = NotificationCommentReply::where('user_id', Auth::id())
            ->with('comment.user.account')
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->getCommentNotificationDTOs($notifications);
    }

    protected function getCommentNotificationDTOs($notifications)
    {
        return $notifications->map(function ($notification) {
            return new CommentNotificationDTO(
                $notification->id,
                new UserDTO(
                    $notification->comment->user->id,
                    $notification->comment->user->name,
                    $notification->comment->user->account->image,
                ),
                $notification->comment->post_id,
                $notification->comment->text,
                $notification->created_at
            );
        });
    }

    public function getReposts()
    {
        $notifications = NotificationRepost::where('user_id', Auth::id())
            ->with('post.user.account')
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->getRepostNotificationDTOs($notifications);
    }

    protected function getRepostNotificationDTOs($notifications)
    {
        return $notifications->map(function ($notification) {
            return new RepostNotificationDTO(
                $notification->id,
                new UserDTO(
                    $notification->post->user->id,
                    $notification->post->user->name,
                    $notification->post->user->account->image,
                ),
                $notification->post_id,
                $notification->post->repost_id,
                $notification->create_at
            );
        });
    }
}
