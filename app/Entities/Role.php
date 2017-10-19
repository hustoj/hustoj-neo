<?php

namespace App\Entities;

use Zizaco\Entrust\EntrustRole;

/**
 * Class Role
 *
 * @package App\Entities
 *
 * @property string $name
 * @property string $display_name
 * @property string $description
 */
class Role extends EntrustRole
{
    protected $fillable = ['name', 'display_name', 'description'];
}
