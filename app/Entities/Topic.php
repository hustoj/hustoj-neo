<?php

namespace App\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Topic.
 *
 * @property int $id
 * @property int $contest_id
 * @property int $problem_id
 * @property int $user_id
 * @property string $title
 * @property string $content
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Reply[]|Collection $replies
 */
class Topic extends Model
{
    use SoftDeletes;
    use CustomDateFormat;

    protected $fillable = [
        'contest_id',
        'user_id',
        'problem_id',
        'title',
        'content',
    ];

    public function showProblemId()
    {
        if ($this->problem_id < 100 && $this->contest_id) {
            return show_order($this->problem_id);
        }

        return $this->problem_id;
    }

    public function title($default = 'untitle')
    {
        if ($this->title) {
            return $this->title;
        }

        return $default;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contest()
    {
        return $this->belongsTo(Contest::class);
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }
}
