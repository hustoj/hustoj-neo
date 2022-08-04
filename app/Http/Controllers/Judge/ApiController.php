<?php

namespace App\Http\Controllers\Judge;

use App\Entities\Problem;
use App\Entities\Solution;
use App\Http\Controllers\Controller;
use App\Http\Requests\Judger\JudgerRequest;
use App\Http\Requests\Judger\ReportRequest;
use App\Services\DataProvider;
use LogicException;

class ApiController extends Controller
{
    /** @var \App\Entities\Judger */
    public $judger;

    /**
     * ApiController constructor.
     *
     * @param  JudgerRequest  $request
     *
     * @throws \App\Exceptions\Judger\JudgerCodeInvalid
     */
    public function __construct(JudgerRequest $request)
    {
        $request->validate();
        $this->judger = $request->getJudger();
    }

    public function data(JudgerRequest $request)
    {
        $pid = $request->input('pid');
        Problem::query()->findOrFail($pid);

        try {
            /** @var DataProvider $dp */
            $dp = app(DataProvider::class);

            return $dp->getData($pid);
        } catch (LogicException $e) {
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
