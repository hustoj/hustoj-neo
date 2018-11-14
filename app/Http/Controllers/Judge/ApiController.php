<?php

namespace App\Http\Controllers\Judge;

use App\Entities\Solution;
use App\Exceptions\Judger\JudgerCodeInvalid;
use App\Http\Controllers\Controller;
use App\Http\Requests\Judger\JudgerRequest;
use App\Http\Requests\Judger\ReportRequest;
use App\Http\Requests\Request;
use App\Services\DataProvider;
use App\Services\JudgerService;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class ApiController extends Controller
{
    /** @var \App\Entities\Judger */
    public $judger;

    /**
     * ApiController constructor.
     *
     * @param \App\Http\Requests\Judger\JudgerRequest $request
     *
     * @throws \App\Exceptions\Judger\JudgerCodeInvalid
     */
    public function __construct(JudgerRequest $request)
    {
        $code = $request->judgeCode();
        $this->checkCodeValid($code);
        $this->beat();
    }

    /**
     * @param $code
     *
     * @throws \App\Exceptions\Judger\JudgerCodeInvalid
     */
    private function checkCodeValid($code)
    {
        if ($code) {
            $this->judger = app(JudgerService::class)->getJudger($code);
            if ($this->judger) {
                return;
            }
            info('Judger Request, Judger Code Invalid:', ['code' => $code]);
        }
        throw new JudgerCodeInvalid();
    }

    public function data(Request $request)
    {
        $pid = $request->input('pid');

        try {
            /** @var DataProvider $dp */
            $dp = app(DataProvider::class);
            $dataInput = $dp->getProblemData($pid, DataProvider::TYPE_IN);
            $dataOutput = $dp->getProblemData($pid, DataProvider::TYPE_OUT);

            return [
                'input'  => $dataInput,
                'output' => $dataOutput,
            ];
        } catch (FileNotFoundException $e) {
            return [
                'message' => $e->getMessage(),
            ];
        }
    }

    public function report(ReportRequest $request)
    {
        /** @var Solution $solution */
        $solution = Solution::query()->find($request->getSolutionId());
        if ($solution) {
            $solution->result = $request->input('status');
            if ($request->input('memory_cost')) {
                $solution->memory_cost = $request->input('memory_cost');
            }
            if ($request->input('time_cost')) {
                $solution->time_cost = $request->input('time_cost');
            }
            if ($solution->isRuntimeError() && $request->input('runtime_info')) {
                if ($info = $solution->runtimeInfo) {
                    $info->delete();
                }
                $solution->runtimeInfo()->create(['content' => $request->input('runtime_info')]);
            }
            if ($solution->isCompileError() && $request->input('compile_info')) {
                if ($info = $solution->compileInfo) {
                    $info->delete();
                }
                $solution->compileInfo()->create(['content' => $request->input('compile_info')]);
            }
            $solution->save();
        }

        return [
            'code' => 0,
        ];
    }

    public function heartbeat()
    {
        return '';
    }

    private function beat()
    {
        $redis = app('redis');
        if ($redis && $this->judger) {
            $key = implode(':', ['judger', 'beat']);
            $redis->hset($key, $this->judger->id, time());
        }
    }
}
