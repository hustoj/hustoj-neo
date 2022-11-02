<?php

namespace App\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Log.
 *
 *
 * @property int $id
 * @property int $user_id
 * @property string $ip
 * @property string $password
 * @property int $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class LoginLog extends Model
{
    use CustomDateFormat;
    public const ST_OK = 0;
    public const ST_FAILED = 1;

    protected $table = 'logging';

    protected $fillable = [
        'user_id',
        'ip',
        'status',
        'password',
    ];
}
