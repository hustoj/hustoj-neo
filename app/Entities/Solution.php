<?php

namespace App\Entities;

use App\Language;
use App\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Solution.
 *
 * @property int $id
 * @property int $problem_id
 * @property int $order
 * @property int $contest_id
 * @property int $user_id
 * @property int $time_cost
 * @property int $memory_cost
 * @property int $language
 * @property int $result
 * @property string $ip
 * @property int $code_length
 * @property Carbon $judged_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Contest $contest
 * @property User $user
 * @property Problem $problem
 * @property-read Source $source
 * @property-read \App\Entities\CompileInfo $compileInfo
 * @property-read \App\Entities\RuntimeInfo $runtimeInfo
 */
class Solution extends Model
{
    use CustomDateFormat;
    public static $status = [
        4  => 'Accepted',
        5  => 'Presentation Error',
        6  => 'Wrong Answer',
        7  => 'Time Limit Exceed',
        8  => 'Memory Limit Exceed',
        9  => 'Output Limit Exceed',
        10 => 'Runtime Error',
        11 => 'Compile Error',
        12 => 'Compile OK',
        13 => 'Test Running Done',
        0  => 'Pending',
        1  => 'Pending Rejudging',
        2  => 'Compiling',
        3  => 'Running &amp; Judging',
    ];
    protected $fillable = [
        'problem_id',
        'user_id',
        'language',
        'ip',
        'order',
        'contest_id',
        'code',
        'result',
        'time_cost',
        'memory_cost',
        'code_length',
        'judged_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Contest
     */
    public function contest()
    {
        return $this->belongsTo(Contest::class, 'contest_id');
    }

    public function problem()
    {
        return $this->belongsTo(Problem::class, 'problem_id');
    }

    public function isContest()
    {
        return $this->contest_id > 0;
    }

    public function compileInfo()
    {
        return $this->hasOne(CompileInfo::class, 'solution_id', 'id');
    }

    public function runtimeInfo()
    {
        return $this->hasOne(RuntimeInfo::class, 'solution_id', 'id');
    }

    public function source()
    {
        return $this->hasOne(Source::class, 'solution_id', 'id');
    }

    public function lang()
    {
        return Language::showLang($this->language);
    }

    public function result()
    {
        return self::$status[$this->result];
    }

    public function isCompileError()
    {
        return $this->result === Status::COMPILE_ERROR;
    }

    public function isRuntimeError()
    {
        return $this->result === Status::RUNTIME_ERROR;
    }

    public function isFailed()
    {
        // 既不是AC，也不是等待评测，就挂了
        return ! $this->isAccepted() && ! $this->isPending();
    }

    public function isAccepted()
    {
        return $this->result === Status::ACCEPT;
    }

    public function isPending()
    {
        return $this->result === Status::PENDING;
    }

    /**
     * Get the Solution's author.
     *
     * @return User|\Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
