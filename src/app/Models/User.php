<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_image',
        'postal_code',
        'address',
        'building_name',
        'email_verified_at',
        'email_verified_token',
        'profile_completed',        
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //グッズモデルとのリレーション
    public function goods()
    {
        return $this->hasMany(Good::class);
    }

    //いいねとのリレーション
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    //メール認証の際に必ずtokenが生成されるようにする
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($user) {
            if (empty($user->email_verification_token)) {
                $user->email_verification_token = Str::random(64);
            }
        });
    }
}

