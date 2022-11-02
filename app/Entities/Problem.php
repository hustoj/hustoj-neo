<?php

namespace App\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Problem.
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $input
 * @property string $output
 * @property string $sample_input
 * @property string $sample_output
 * @property string $hint
 * @property string $source
 * @property int $status
 * @property int $time_limit
 * @property int $memory_limit
 * @property int $submit
 * @property int $accepted
 * @property int $spj
 * @property string $memo
 * @property int $user_id
 * @property User $author
 * @property Solution[]|Collection $solutions
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 */
class Problem extends Model
{
    use SoftDeletes;
    use CustomDateFormat;

    public const ST_NORMAL = 0;
    public const ST_HIDE = 1;

    protected $fillable = [
        'title',
        'description',
        'input',
        'output',
        'sample_input',
        'sample_output',
        'spj',
        'hint',
        'source',
        'time_limit',
        'memory_limit',
        'accepted',
        'submit',
        'status',
        'memo',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * is problem special judge.
     *
     * @return bool
     */
    public function isSpecialJudge()
    {
        return $this->spj === 1;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function solutions()
    {
        return $this->hasMany(Solution::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany|Contest[]
     */
    public function contests()
    {
        return $this->belongsToMany(Contest::class);
    }

    public function order()
    {
        if (isset($this->pivot)) {
            return show_order($this->pivot->order);
        }

        return $this->id;
    }

    public function isAvailable()
    {
        return $this->status === self::ST_NORMAL;
    }
}
