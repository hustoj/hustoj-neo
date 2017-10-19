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
    protected $table = 'source_code';
    public $timestamps = false;

    protected $fillable = [
        'solution_id',
        'code',
    ];
}
