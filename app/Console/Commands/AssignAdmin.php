<?php

namespace App\Console\Commands;

use App\Entities\Role;
use App\Entities\User;
use App\Services\UserService;
use Illuminate\Console\Command;

class AssignAdmin extends Command
{
    protected $signature = 'assign:admin';

    public function handle()
    {
        $this->initial();
    }

    private function initial()
    {
        while (true) {
            $username = $this->ask('You should input administrator username');
            /** @var User $user */
            $user = app(UserService::class)->findByName($username);
            if ($user) {
                $this->info('User Id is '.$user->id);
                $this->assignAdmin($user);
                $this->info('Assign Done');
                break;
            }
            $this->error('User is not found!');
        }
    }

    /**
     * @param  User  $user
     */
    private function assignAdmin($user)
    {
        $role = $this->getAdminRole();
        $user->roles()->attach($role);
    }

    /**
     * @return Role|mixed
     */
    private function getAdminRole()
    {
        $role = Role::where('name', 'admin')->first();
        if (! $role) {
            $role = $this->initialRole();
        }

        return $role;
    }

    private function initialRole()
    {
        $role = new Role();
        $role->name = 'admin';
        $role->display_name = 'Administrator';
        $role->save();

        return $role;
    }
}
