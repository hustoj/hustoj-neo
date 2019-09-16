<?php

namespace App\Entities;

use Carbon\Carbon;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laratrust\Traits\LaratrustUserTrait;

/**
 * Class User.
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $nick
 * @property string $school
 * @property string $locale
 * @property int $language
 * @property string $password
 * @property string $remember_token
 * @property int $submit
 * @property int $email_level
 * @property int $solved
 * @property int $status
 * @property Carbon $email_verified_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class User extends Authenticatable implements MustVerifyEmailContract
{
    use Notifiable, LaratrustUserTrait, MustVerifyEmail;

    const ST_ACTIVE = 0;
    const ST_INACTIVE = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'email',
        'school',
        'nick',
        'email_level',
        'confirmed',
        'language',
        'submit',
        'solved',
        'locale',
        'language',
        'status',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function lastAccess()
    {
        return $this->logs()->orderBy('created_at', 'desc')->limit(1);
    }

    public function showEmail()
    {
        return $this->email_level != 0;
    }

    public function getEmail()
    {
        if ($this->email_level == 2) {
            return base64_encode($this->email);
        }
        return $this->email;
    }

    public function logs()
    {
        return $this->hasMany(LoginLog::class, 'user_id');
    }

    public function contests()
    {
        return $this->belongsToMany(Contest::class, 'contest_user', 'user_id');
    }

    public function isActive()
    {
        return $this->status === self::ST_ACTIVE;
    }
}
