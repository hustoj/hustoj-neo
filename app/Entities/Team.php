<?php

namespace App\Entities;

use Carbon\Carbon;

class Team
{
    protected $wa_counts = [];
    /** @var Carbon[] */
    protected $time_ac = [];

    protected $user;

    protected $number_of_ac = 0;
    protected $number_of_submit = 0;
    protected $cost_time;

    /** @var Contest */
    protected $contest;
    private $number_of_problem;

    public function __construct(Contest $contest)
    {
        $this->contest = $contest;

        $this->number_of_problem = $contest->problems->count();

        for ($i = 0; $i < $this->number_of_problem; $i++) {
            $this->wa_counts[$i] = 0;
            $this->time_ac[$i] = null;
        }
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function user()
    {
        return $this->user;
    }

    /**
     * 添加新的solution，统计数据.
     *
     * @param Solution $solution
     */
    public function addSolution(Solution $solution)
    {
        if ($this->number_of_problem === 0) {
            return;
        }
        $this->number_of_submit++;

        if ($solution->isAccepted()) {
            $this->markProblemAccept($solution);
        }

        if ($solution->isFailed()) {
            $this->addFailedCount($solution->order);
        }
    }

    private function addFailedCount($order)
    {
        if (!array_key_exists($order, $this->wa_counts)) {
            $this->wa_counts[$order] = 0;
        }
        $this->wa_counts++;
    }

    /**
     * 获取通过的题目数.
     *
     * @return int
     */
    public function getNumberOfAccept()
    {
        return $this->number_of_ac;
    }

    public function getNumberOfSubmit()
    {
        return $this->number_of_submit;
    }

    /**
     * 获取题目的尝试次数.
     *
     * @param $pid
     *
     * @return mixed
     */
    public function getProblemWACount($pid)
    {
        return $this->wa_counts[$pid];
    }

    public function getProblemAcceptTime($pid)
    {
        if (null === $this->time_ac[$pid]) {
            return 0;
        }

        $start = new Carbon($this->contest->start_time);

        return $this->time_ac[$pid]->diffInSeconds($start);
    }

    public function isProblemAccept($pid)
    {
        return null !== $this->time_ac[$pid];
    }

    /**
     * 获取总时间.
     *
     * @return int|mixed
     */
    public function getTotalTime()
    {
        if (null === $this->cost_time) {
            $total = 0;
            $number_of_problem = $this->contest->problems->count();
            for ($i = 0; $i < $number_of_problem; $i++) {
                $accepted_at = $this->getProblemAcceptTime($i);
                if ($accepted_at === 0) {
                    continue;
                }

                // 只有通过的题目才需要计算时间和罚时
                $total += $accepted_at;
                $total += $this->getPenaltyOfProblem($i);
            }
            $this->cost_time = $total;
        }

        return $this->cost_time;
    }

    /**
     * 获取总罚时.
     *
     * @return int|mixed
     */
    public function getTotalPenaltyTime()
    {
        $total = 0;
        $number_of_problem = $this->contest->problems->count();
        for ($i = 0; $i < $number_of_problem; $i++) {
            $total += $this->getPenaltyOfProblem($i);
        }

        return $total;
    }

    /**
     * 获取单个题目的罚时.
     *
     * @param $pid
     *
     * @return mixed
     */
    public function getPenaltyOfProblem($pid)
    {
        return $this->getProblemWACount($pid) * 20;
    }

    /**
     * @param Solution $solution
     */
    private function markProblemAccept($solution)
    {
        $pid = $solution->order;
        if (!array_key_exists($pid, $this->time_ac) || $this->time_ac[$pid] === null) {
            $this->time_ac[$pid] = $solution->created_at;
            $this->number_of_ac++;
        } else {
            if ($this->time_ac[$pid]->gt($solution->created_at)) {
                $this->time_ac[$pid] = $solution->created_at;
            }
        }
    }
}
