<?php

namespace App\Console\Commands\Migrations;

use App\Entities\Option;
use Illuminate\Console\Command;

class OptionMigration extends Migration
{
    /**
     * @param  Command  $command
     */
    public function handle($command)
    {
        $command->info('Migrating Options...');
        $options = $this->table('options')->get();
        foreach ($options as $option) {
            $newOption = new Option();
            $newOption->key = $option->name;
            $newOption->description = $option->desc;
            $newOption->value = $option->value;
            $newOption->category = 'default';
            $newOption->save();
        }

        $command->info('Migrating Options Done');
    }
}
