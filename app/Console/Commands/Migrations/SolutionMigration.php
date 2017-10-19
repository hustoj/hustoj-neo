<?php

namespace App\Console\Commands\Migrations;

use App\Entities\CompileInfo;
use App\Entities\RuntimeInfo;
use App\Entities\Solution;
use App\Entities\Source;
use App\Services\UserService;

class SolutionMigration extends Migration
{
    public function handle($command)
    {
        $command->info('Migrating Solutions...');
        $this->table('solution')->orderBy('solution_id', 'asc')->chunk(100, function ($objects) {
            foreach ($objects as $object) {
                $this->transformSolution($object);
            }
        });
        $command->info('Migrating Runtime Info...');
        $this->table('runtimeinfo')->orderBy('solution_id', 'asc')->chunk(100, function ($objects) {
            foreach ($objects as $object) {
                $this->transformRuntimeInfo($object);
            }
        });
        $command->info('Migrating Compile Info...');
        $this->table('compileinfo')->orderBy('solution_id', 'asc')->chunk(100, function ($objects) {
            foreach ($objects as $object) {
                $this->transformCompileInfo($object);
            }
        });
        $command->info('Migrating Source Code...');
        $this->table('source_code')->orderBy('solution_id', 'asc')->chunk(100, function ($objects) {
            foreach ($objects as $object) {
                $this->transformSource($object);
            }
        });

        $command->info('Migrating Solutions Done');
    }

    private function transformSolution($oldSolution)
    {
        $solution = new Solution();
        $solution->fill(get_object_vars($oldSolution));
        $solution->id = $oldSolution->solution_id;
        $solution->order = $oldSolution->num;
        $solution->time_cost = $oldSolution->time;
        $solution->memory_cost = $oldSolution->memory;
        $solution->language = $oldSolution->language;
        $user = app(UserService::class)->findByName($oldSolution->user_id);
        $solution->user_id = $user->id;
        $solution->created_at = $oldSolution->in_date;
        $solution->judged_at = $oldSolution->judgetime;
        $solution->save();
    }

    private function transformRuntimeInfo($object)
    {
        $info = new RuntimeInfo();
        $info->solution_id = $object->solution_id;
        $info->content = $object->error;
        $info->save();
    }

    private function transformCompileInfo($object)
    {
        $info = new CompileInfo();
        $info->solution_id = $object->solution_id;
        $info->content = $object->error;
        $info->save();
    }

    private function transformSource($object)
    {
        $info = new Source();
        $info->solution_id = $object->solution_id;
        $info->code = $object->source;
        $info->save();
    }
}
