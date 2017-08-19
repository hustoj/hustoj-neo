<?php namespace App\Entities;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

/**
 * Class User
 *
 * @package App\Entities
 * @property int    $id
 * @property string $username
 * @property string $email
 * @property string $nick
 * @property string $school
 * @property string $locale
 * @property int    $language
 * @property string $password
 * @property string $remember_token
 * @property int    $submit
 * @property int    $solved
 * @property int    $defunct
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class User extends Authenticatable
{
    use Notifiable, EntrustUserTrait;

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
        'confirmed',
        'language',
        'submit',
        'solved',
        'locale',
        'language',
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

    public function last_access()
    {
        return $this->logs()->orderBy('created_at', 'desc')->limit(1);
    }

    public function logs()
    {
        return $this->hasMany(LoginLog::class, 'user_id');
    }

    public function contests()
    {
        return $this->belongsToMany(Contest::class, 'contest_user', 'user_id');
    }
}
