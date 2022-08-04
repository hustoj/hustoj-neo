<?php

namespace App\Services;

use App\Entities\Contest;
use App\Entities\Solution;
use App\Entities\Team;
use App\Entities\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class Ranking
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

    /**
     * 在比赛开始和结束之间的提交才是比赛的合法提交.
     *
     * @param  Contest  $contest
     * @return Collection|Solution[]
     */
    private function getValidSolutions($contest)
    {
        $columns = ['created_at', 'order', 'user_id', 'result', 'id'];
        $start_at = Carbon::parse($contest->start_time)->subSeconds(1);
        $end_at = Carbon::parse($contest->end_time)->addSeconds(1);

        return $contest->solutions()->whereBetween('created_at', [$start_at, $end_at])->get($columns);
    }

    private function addSolution(Solution $solution)
    {
        if (! array_key_exists($solution->user_id, $this->teams)) {
            $this->teams[$solution->user_id] = new Team($this->contest);
        }

        $this->teams[$solution->user_id]->addSolution($solution);
    }

    /**
     * @return Team[]
     */
    public function result()
    {
        $this->initTeam();

        /** @var $result Team[] */
        $result = array_values($this->teams);
        usort($result, function ($team1, $team2) {
            return ! $this->isBetter($team1, $team2);
        });

        return $result;
    }

    private function initTeam()
    {
        foreach ($this->getTeams() as $user) {
            $this->teams[$user->id]->setUser($user);
        }
    }

    /**
     * @return Collection|User[]
     */
    protected function getTeams()
    {
        return User::query()->whereIn('id', $this->getTeamsId())->get();
    }

    /**
     * @return array
     */
    private function getTeamsId()
    {
        return array_keys($this->teams);
    }

    /**
     * @param  Team  $team
     * @param  Team  $current
     * @return bool
     */
    private function isBetter($team, $current)
    {
        // accept more
        if ($team->numberOfAccept() > $current->numberOfAccept()) {
            return true;
        }
        // accept equal, but penalty less
        if ($team->numberOfAccept() == $current->numberOfAccept()
            && $team->totalPenalty() < $current->totalPenalty()) {
            return true;
        }

        return false;
    }

    protected function insertElementAtIndex($arr, $ele, $index)
    {
        $start = array_slice($arr, 0, $index);
        $end = array_slice($arr, $index);
        $start[] = $ele;

        return array_merge($start, $end);
    }
}
