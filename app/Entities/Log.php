<?php namespace App\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Log
 *
 * @package App\Entities
 *
 * @property int    $id
 * @property int    $user_id
 * @property string $ip
 * @property int    $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Log extends Model
{
    const ST_OK     = 0;
    const ST_FAILED = 1;

    protected $table = 'logging';

    protected $fillable = [
        'user_id',
    ];
}