<?php

namespace App\Console\Commands\Migrations;

use App\Entities\User;

class UserMigration extends Migration
{
    public function handle($command)
    {
        $command->info('Migrating Users...');
        $this->table('users')->orderBy('reg_time', 'asc')->chunk(100, function ($objects) {
            foreach ($objects as $object) {
                $this->transform($object);
            }
        });
        $command->info('Migrating Users Done');
    }

    private function transform($object)
    {
        $user = new User();
        $user->fill(get_object_vars($object));
        $user->username = $object->user_id;
        $user->created_at = $object->reg_time;
        $user->updated_at = $object->reg_time;
        $user->password = $object->password;
        $user->status = User::ST_ACTIVE;
        if ($object->defunct === 'Y') {
            $user->status = User::ST_INACTIVE;
        }
        $user->save();
    }
}
