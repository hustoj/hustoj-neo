<?php

namespace App\Console\Commands\Migrations;

use App\Entities\Problem;

class ProblemMigration extends Migration
{
    public function handle($command)
    {
        $command->info('Migrating Problems...');
        $this->table('problem')->orderBy('problem_id', 'asc')->chunk(100, function ($objects) {
            $objects->map(function ($object) {
                $this->transform($object);
            });
        });
        $command->info('Migrating Problems Done');
    }

    private function transform($object)
    {
        $problem = new Problem();
        $problem->fill(get_object_vars($object));
        $problem->id = $object->problem_id;
        $problem->title = $object->title;
        $problem->description = $object->description;
        $problem->created_at = $object->in_date;
        if ($problem->submit === null) {
            $problem->submit = 0;
        }
        if ($problem->accepted === null) {
            $problem->accepted = 0;
        }

        $problem->save();

        if ($object->defunct === 'Y') {
            $problem->delete();
        }
    }
}
