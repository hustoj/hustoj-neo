<?php

namespace App\Listeners;

use Illuminate\Database\Events\QueryExecuted;

class DatabaseListener
{
    public function handle(QueryExecuted $event)
    {
        $sql = str_replace('%', '%%', $event->sql);
        $sql = str_replace('?', '\'%s\'', $sql);

        $message = vsprintf($sql, $event->bindings);

        info($message);
    }
}
