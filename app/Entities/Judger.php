<?php

namespace App\Entities;

use Database\Factories\JudgerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Judger.
 *
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $code
 * @property int $status
 * @property string $bind_ip
 * @property int $category
 */
class Judger extends Model
{
    /** @use HasFactory<JudgerFactory> */
    use HasFactory;

    public const ST_ACTIVITY = 1;
    public const ST_DEACTIVATE = 0;

    protected $fillable = [
        'name',
        'description',
        'code',
        'status',
        'category',
    ];

    protected static function newFactory(): JudgerFactory
    {
        return JudgerFactory::new();
    }
}
