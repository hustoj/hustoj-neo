<?php

namespace App\Services;

use App\Entities\User;
use Illuminate\Database\Eloquent\Collection;

class AdminChecker
{
    /**
     * @var User[]|Collection
     */
    private $admin_users = [];

    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        $this->admin_users = User::whereRoleIs('admin')->get();
    }

    public function isAdmin(User $user)
    {
        if ($this->admin_users == null) {
            $this->init();
        }
        foreach ($this->admin_users as $admin_user) {
            if ($admin_user->id == $user->id) {
                return true;
            }
        }

        return false;
    }
}
