<?php

namespace App\Entities;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Reply.
 *
 * @property int $id
 * @property int $user_id
 * @property int $topic_id
 * @property string $content
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Reply extends Model
{
    use SoftDeletes;
    use CustomDateFormat;

    protected $fillable = [
        'user_id',
        'topic_id',
        'content',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }
}
