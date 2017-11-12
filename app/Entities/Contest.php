<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class Contest.
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property Carbon $start_time
 * @property Carbon $end_time
 * @property int $user_id
 * @property int $hidden
 * @property bool $private
 * @property-read Collection|Problem[] $problems
 * @property-read Collection|User[] $users
 */
class Contest extends Model
{
    use SoftDeletes;
    const ST_PRIVATE = 1;
    const ST_PUBLIC  = 0;

    protected $dates = [
        'start_time',
        'end_time',
    ];

    protected $fillable = [
        'id',
        'title',
        'description',
        'start_time',
        'end_time',
        'user_id',
        'hidden',
        'private',
    ];

    public function isOpen()
    {
        $now = new Carbon();

        return $now->between($this->start_time, $this->end_time);
    }

    public function isEnd()
    {
        return Carbon::now()->gt($this->end_time);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function problems()
    {
        return $this->belongsToMany(Problem::class)->withPivot('order')->orderBy('order', 'asc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questions()
    {
        return $this->hasMany(Topic::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function solutions()
    {
        return $this->hasMany(Solution::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'contest_user', 'contest_id');
    }
}
