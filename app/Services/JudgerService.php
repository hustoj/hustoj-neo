<?php

namespace App\Services;

use App\Entities\Judger;
use App\Exceptions\Judger\JudgerNameExist;

class JudgerService
{
    /**
     * @param  string  $name
     * @param  null  $bind_ip
     * @return \App\Entities\Judger
     *
     * @throws \App\Exceptions\Judger\JudgerNameExist
     */
    public function newJudger($name, $bind_ip = null)
    {
        if ($this->exist($name)) {
            throw new JudgerNameExist();
        }
        $judger = new Judger();
        $judger->name = $name;
        $judger->code = new_judge_code();
        $judger->status = Judger::ST_ACTIVITY;
        if ($bind_ip) {
            $judger->bind_ip = $bind_ip;
        }
        $judger->save();

        return $judger;
    }

    private function exist($name)
    {
        return Judger::query()->where('name', $name)->first();
    }

    /**
     * @param  $code
     * @return Judger
     */
    public function getJudger($code)
    {
        return Judger::query()->where('code', $code)->first();
    }
}
