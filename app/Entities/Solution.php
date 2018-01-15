<?php

namespace App\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Solution.
 *
 * @property int     $id
 * @property int     $problem_id
 * @property int     $order
 * @property int     $contest_id
 * @property int     $user_id
 * @property int     $time_cost
 * @property int     $memory_cost
 * @property int     $language
 * @property int     $result
 * @property string  $ip
 * @property int     $code_length
 * @property Carbon  $judged_at
 * @property Carbon  $created_at
 * @property Carbon  $updated_at
 * @property Contest $contest
 * @property User $user
 * @property Problem $problem
 * @property-read Source $source
 */
class Solution extends Model
{
    const STATUS_PENDING = 0;
    const STATUS_PENDING_REJUDGE = 1;
    const STATUS_COMPILE = 2;
    const STATUS_REJUDGING = 3;
    const STATUS_AC = 4;
    const STATUS_PE = 5;
    const STATUS_WA = 6;
    const STATUS_TLE = 7;
    const STATUS_MLE = 8;
    const STATUS_OLE = 9;
    const STATUS_RE = 10;
    const STATUS_CE = 11;
    const STATUS_COMPILE_OK = 12;
    const STATUS_TEST_RUN = 13;

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

    public static $languages = [
        0 => 'C',
        1 => 'C++',
        2 => 'Java',
        3 => 'Pascal',
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
        return self::$languages[$this->language];
    }

    public function result()
    {
        return self::$status[$this->result];
    }

    public function isCompileError()
    {
        return $this->result === self::STATUS_CE;
    }

    public function isRuntimeError()
    {
        return $this->result === self::STATUS_RE;
    }

    public function isPending()
    {
        return $this->result === self::STATUS_PENDING;
    }

    public function isAccepted()
    {
        return $this->result === self::STATUS_AC;
    }

    public function isFailed()
    {
        // 既不是AC，也不是等待评测，就挂了
        return !$this->isAccepted() && !$this->isPending();
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
