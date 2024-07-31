<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens,
        HasFactory,
        Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */

    protected $casts = [
        'password' => 'hashed',
    ];

    protected function email(): Attribute{
        return Attribute::make(
          get: fn($value) => $value,
          set: fn($value) => Str::lower($value)
        );
    }
    protected function username(): Attribute{
        return Attribute::make(
            get: fn($value) => $value,
            set: fn($value) => Str::lower($value)
        );
    }

    public function followers()
    {
        return $this->hasMany(Subscription::class, 'user_id');
    }

    public function following()
    {
        return $this->hasMany(Subscription::class, 'follower_id');
    }

    public function account()
    {
        return $this->hasOne(Account::class);
    }
}
