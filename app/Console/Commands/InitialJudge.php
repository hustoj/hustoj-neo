<?php

namespace App\Console\Commands;

use App\Entities\Role;
use App\Entities\User;
use App\Services\UserService;
use Illuminate\Console\Command;

class InitialJudge extends Command
{
    protected $signature = 'judge:initial';

    public function handle()
    {
        $this->initial();
    }

    private function initial()
    {
        $role = $this->initialRole();
        while (true) {
            $username = $this->ask('You should input administrator username');
            /** @var User $user */
            $user = app(UserService::class)->findByName($username);
            if ($user) {
                $user->roles()->attach($role);
                break;
            }
        }

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