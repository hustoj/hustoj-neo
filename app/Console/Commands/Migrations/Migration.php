<?php

namespace App\Console\Commands\Migrations;

use Illuminate\Console\Command;

abstract class Migration
{
    /**
     * @param  Command  $command
     */
    abstract public function handle($command);

    protected function table($table)
    {
        return $this->getConnection()->table($table);
    }

    protected function getConnection()
    {
        return app('db')->connection('old');
    }
}
