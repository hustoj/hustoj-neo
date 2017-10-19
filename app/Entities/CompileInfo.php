<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CompileInfo
 *
 * @package App\Entities
 * @property int $id
 * @property int $solution_id
 * @property string $content
 */
class CompileInfo extends Model
{
    protected $table = 'compile_info';

    protected $fillable = [
        'solution_id',
        'content',
    ];
}
