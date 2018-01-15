<?php

namespace App\Services;

use App\Entities\Contest;
use App\Entities\Solution;
use App\Entities\Team;
use App\Entities\User;
use App\Repositories\Criteria\WhereIn;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class StandingService
{
    /** @var Team[] */
    private $teams = [];
    private $contest;

    public function __construct(Contest $contest)
    {
        $this->contest = $contest;

        $solutions = $this->getValidSolutions($contest);

        foreach ($solutions as $solution) {
            $this->addSolution($solution);
        }
    }

    private function addSolution(Solution $solution)
    {
        if (!array_key_exists($solution->user_id, $this->teams)) {
            $this->teams[$solution->user_id] = new Team($this->contest);
        }

        $this->teams[$solution->user_id]->addSolution($solution);
    }

    /**
     * @return Team[]
     */
    public function result()
    {
        /** @var $result Team[] */
        $result = [];

        foreach ($this->getTeams() as $user) {
            $this->teams[$user->id]->setUser($user);
        }

        foreach ($this->teams as $team) {
            $inserted = false;
            $totalSolutions = count($result);
            for ($i = 0; $i < $totalSolutions; $i++) {
                if ($team->getNumberOfAccept() > $result[$i]->getNumberOfAccept()) {
                    // more
                    $result = $this->insertElementAtIndex($result, $team, $i);
                    $inserted = true;
                    break;
                } elseif ($team->getNumberOfAccept() < $result[$i]->getNumberOfAccept()) {
                    // less
                    continue;
                } else {
                    // eq, then decide by time cost
                    if ($team->getTotalTime() < $result[$i]->getTotalTime()) {
                        $result = $this->insertElementAtIndex($result, $team, $i);
                        $inserted = true;
                        break;
                    }
                }
            }
            if (!$inserted) {
                $result[] = $team;
            }
        }

        return $result;
    }

    /**
     * @return Collection|User[]
     */
    protected function getTeams()
    {
        $repo = app(UserRepository::class);
        $whereIn = new WhereIn('id', array_keys($this->teams));
        $repo->pushCriteria($whereIn);

        return $repo->all();
    }

    protected function insertElementAtIndex($arr, $ele, $index)
    {
        $start = array_slice($arr, 0, $index);
        $end = array_slice($arr, $index);
        $start[] = $ele;

        return array_merge($start, $end);
    }

    /**
     * 在比赛开始和结束之间的提交才是比赛的合法提交.
     *
     * @param Contest $contest
     *
     * @return Collection|Solution[]
     */
    private function getValidSolutions($contest)
    {
        $columns = ['created_at', 'order', 'user_id', 'result', 'id'];
        $start_at = Carbon::parse($contest->start_time)->subSeconds(1);
        $end_at = Carbon::parse($contest->end_time)->addSeconds(1);

        return $contest->solutions()->whereBetween('created_at', [$start_at, $end_at])->get($columns);
    }
}
