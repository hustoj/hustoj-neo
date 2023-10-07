<?php

namespace App\Entities;

use Carbon\Carbon;

class Team
{
    protected $waCounts = [];
    /** @var Carbon[] */
    protected $timePassed = [];

    /** @var \App\Entities\User */
    protected $user;

    protected $accepts = 0;
    protected $submits = 0;

    /** @var Contest */
    protected $contest;
    private $problemCount;

    public function __construct(Contest $contest)
    {
        $this->contest = $contest;

        $this->problemCount = $contest->problems->count();

        for ($i = 0; $i < $this->problemCount; $i++) {
            $this->waCounts[$i] = 0;
            $this->timePassed[$i] = null;
        }
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function user(): User
    {
        return $this->user;
    }

    /**
     * 添加新的solution，统计数据.
     *
     * @param  Solution  $solution
     */
    public function addSolution(Solution $solution)
    {
        if ($this->problemCount === 0) {
            return;
        }
        $this->submits++;

        if ($solution->isAccepted()) {
            $this->markProblemAccept($solution);
        }

        if ($solution->isFailed()) {
            $this->addFailedCount($solution);
        }
    }

    /**
     * @param  Solution  $solution
     */
    private function markProblemAccept($solution)
    {
        $pid = $solution->order;
        if (! array_key_exists($pid, $this->timePassed) || $this->timePassed[$pid] === null) {
            $this->timePassed[$pid] = $solution->created_at;
            $this->accepts++;
        } else {
            if ($this->timePassed[$pid]->gt($solution->created_at)) {
                $this->timePassed[$pid] = $solution->created_at;
            }
        }
    }

    private function addFailedCount(Solution $solution)
    {
        if (! array_key_exists($solution->order, $this->waCounts)) {
            $this->waCounts[$solution->order] = 0;
        }

        if ($this->shouldMarkAsPenalty($solution)) {
            $this->waCounts[$solution->order]++;
        }
    }

    private function shouldMarkAsPenalty(Solution $solution): bool
    {
        if (array_key_exists($solution->order, $this->timePassed)) {
            if ($this->timePassed[$solution->order]
                && $this->timePassed[$solution->order]->gt($solution->created_at)) {
                return false;
            }
        }

        return true;
    }

    /**
     * 获取通过的题目数.
     *
     * @return int
     */
    public function numberOfAccept()
    {
        return $this->accepts;
    }

    public function getNumberOfSubmit()
    {
        return $this->submits;
    }

    public function isProblemAccept($pid)
    {
        return null !== $this->timePassed[$pid];
    }

    /**
     * 获取总时间.
     *
     * @return int|mixed
     */
    public function totalPenalty()
    {
        $totalTime = 0;
        $number_of_problem = $this->contest->problems->count();
        for ($i = 0; $i < $number_of_problem; $i++) {
            $accepted_at = $this->getProblemAcceptTime($i);
            if ($accepted_at > 0) {
                // 只有通过的题目才需要计算时间和罚时
                $totalTime += $accepted_at;
                $totalTime += $this->getPenaltyOfProblem($i);
            }
        }

        return $totalTime;
    }

    public function getProblemAcceptTime($pid)
    {
        if (null === $this->timePassed[$pid]) {
            return 0;
        }

        $start = new Carbon($this->contest->start_time);

        return $this->timePassed[$pid]->diffInSeconds($start);
    }

    /**
     * 获取单个题目的罚时.
     *
     * @param  $pid
     * @return mixed
     */
    public function getPenaltyOfProblem($pid)
    {
        return $this->getProblemWACount($pid) * 20 * 60;
    }

    /**
     * 获取题目的尝试次数.
     *
     * @param  $pid
     * @return mixed
     */
    public function getProblemWACount($pid)
    {
        return $this->waCounts[$pid];
    }
}
