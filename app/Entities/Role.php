<?php

namespace App\Entities;

use Laratrust\Models\Role as LaratrustRole;

/**
 * Class Role.
 *
 *
 * @property string $name
 * @property string $display_name
 * @property string $description
 */
class Role extends LaratrustRole
{
    protected $fillable = ['name', 'display_name', 'description'];
}
