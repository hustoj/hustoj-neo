<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Option.
 *
 * @property int $id
 * @property string $key
 * @property string $category
 * @property string $description
 * @property string $value
 */
class Option extends Model
{
    protected $table = 'options';

    protected $fillable = [
        'key',
        'category',
        'description',
        'value',
    ];
}
