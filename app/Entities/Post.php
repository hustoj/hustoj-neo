<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Article.
 *
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property int $priority
 * @property int $status
 * @property string $meta_title
 * @property string $meta_description
 * @property string $meta_keywords
 * @property \Carbon\Carbon created_at
 * @property \Carbon\Carbon updated_at
 */
class Post extends Model
{
    use CustomDateFormat;
    protected $table = 'posts';
    protected $fillable = [
        'user_id',
        'slug',
        'title',
        'content',
        'status',
    ];

    public function author()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
