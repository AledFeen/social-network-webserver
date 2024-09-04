<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\Subscription;
use App\Models\User;
use App\Observers\CommentObserver;
use App\Observers\PostLikeObserver;
use App\Observers\PostObserver;
use App\Observers\SubscriptionObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        PostLike::observe(PostLikeObserver::class);
        Comment::observe(CommentObserver::class);
        Subscription::observe(SubscriptionObserver::class);
        Post::observe(PostObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
