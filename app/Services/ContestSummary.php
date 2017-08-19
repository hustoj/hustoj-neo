<?php namespace App\Entities;

/**
 * @property int   acceptedUser
 * @property int   submitUser
 * @property int   total
 * @property array statistics
 */
class ContestSummary
{
    public $acceptedUser = 0;
    public $submitUser   = 0;
    public $total        = 0;

    private $problem;
    /**
     * @var SolutionRepository
     */
    private $solutionRepo;

    static $retInfos = [
        Solution::STATUS_AC,
        Solution::STATUS_PE,
        Solution::STATUS_WA,
        Solution::STATUS_TLE,
        Solution::STATUS_MLE,
        Solution::STATUS_OLE,
        Solution::STATUS_RE,
        Solution::STATUS_CE,
    ];

    private function __construct(Problem $problem)
    {
        $this->solutionRepo = app('App\Repositories\SolutionRepository');
        $this->problem      = $problem;
        $this->clacSummary();
    }

    public static function createFromProblem($problem)
    {
        return new ContestSummary($problem);
    }

    public function clacSummary()
    {
        $oquery = Solution::where('problem_id', '=', $this->problem->id);
//        $this->total = $oquery->count();

        // get accepted user
        $query              = clone $oquery;
        $this->acceptedUser = $query->where('result', '=', Solution::STATUS_AC)->distinct('user_id')->count('user_id');

        // get summit user
        $query            = clone $oquery;
        $this->submitUser = $query->distinct('user_id')->count('user_id');

        $this->statistics = [];
        $resultCount      = $this->getResultCount();
        foreach ($resultCount as $result) {
            $this->data[$result->result] = $result->user_count;
            $this->total                 = $this->total + $result->user_count;
        }
    }

    public function getResultCount()
    {
        $solution = new Solution();
        $query    = \DB::table($solution->getTable())->select(\DB::raw('count(*) as user_count, result'))->where('problem_id',
                '=', $this->problem->id)->groupby('result');

        return $query->get();
    }

    public function bestSolutions()
    {
        return $this->solutionRepo->getBestSolutionOfProblem($this->problem);
    }
}