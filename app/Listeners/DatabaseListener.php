<?php

namespace App\Listeners;

use Illuminate\Database\Events\QueryExecuted;

class DatabaseListener
{
    public function handle(QueryExecuted $event)
    {
//        dd($event);
        $sql = str_replace('%', '%%', $event->sql);
        $sql = str_replace('?', '\'%s\'', $sql);

        $log = vsprintf($sql, $event->bindings);

        info($log);
    }
}
