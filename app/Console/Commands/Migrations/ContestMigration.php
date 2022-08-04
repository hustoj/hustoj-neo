<?php

namespace App\Console\Commands\Migrations;

use App\Entities\Contest;
use App\Entities\User;
use App\Services\UserService;

class ContestMigration extends Migration
{
    public function handle($command)
    {
        $command->info('Migrating Contest...');
        $this->table('contest')->orderBy('contest_id', 'asc')->chunk(100, function ($objects) {
            $objects->map(function ($object) {
                $this->transform($object);
            });
        });
        $command->info('Migrating Contest Done');
    }

    private function transform($object)
    {
        $contest = new Contest();
        $contest->fill(get_object_vars($object));
        $contest->id = $object->contest_id;
        if ($contest->description === null) {
            $contest->description = '';
        }
        $contest->status = 0;
        if ($object->defunct === 'Y') {
            $contest->status = Contest::ST_HIDE;
        }
        $contest->save();

        $this->migrateProblemRelation($contest);

        if ($contest->private) {
            $this->migratePrivilege($contest);
        }
    }

    /**
     * @param  Contest  $contest
     */
    private function migrateProblemRelation($contest)
    {
        $oldRelations = $this->table('contest_problem')->where('contest_id', $contest->id)->get();
        $relations = [];
        foreach ($oldRelations as $relation) {
            $relations[$relation->problem_id] = [
                'order' => $relation->num,
                'title' => $relation->title,
            ];
        }
        $contest->problems()->sync($relations);
    }

    /**
     * @param  Contest  $contest
     */
    private function migratePrivilege($contest)
    {
        $perm = 'c'.$contest->id;
        $perms = $this->table('privilege')->where('rightstr', $perm)->distinct('user_id')->get();

        $perms->map(function ($perm) use ($contest) {
            /** @var User $user */
            $user = app(UserService::class)->findByName($perm->user_id);
            $contest->users()->attach($user);
        });
    }
}
