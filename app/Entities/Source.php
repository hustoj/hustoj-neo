<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Source.
 *
 * @property int $id
 * @property int $solution_id
 * @property string $code
 */
class Source extends Model
{
    public $timestamps = false;
    protected $table = 'source_code';
    protected $fillable = [
        'solution_id',
        'code',
    ];
}
