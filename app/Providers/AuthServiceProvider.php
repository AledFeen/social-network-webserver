<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Notifications\Messages\MailMessage;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {

            $customUrl = str_replace('http://localhost:8000', ' http://localhost:5173', $url);

            return (new MailMessage)
                ->subject('Verify Email address')
                ->line('Click the button below to verify your email address.')
                ->action('Verify Email Address', $customUrl);
        });

        //to change url you need modify here and in ResetPassword->buildMailMessage()
        ResetPassword::createUrlUsing(function (User $user, string $token) {
            return 'http://localhost:5173/reset-password-page/?token='.$token;
        });
    }
}
