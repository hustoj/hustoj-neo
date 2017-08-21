<?php namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Article
 *
 * @package App
 *
 * @property int            $id
 * @property int            $user_id
 * @property string         $title
 * @property string         $slug
 * @property string         $content
 * @property int            $priority
 * @property string         $meta_title
 * @property string         $meta_description
 * @property string         $meta_keywords
 * @property \Carbon\Carbon created_at
 * @property \Carbon\Carbon updated_at
 */
class Post extends Model
{
    protected $table    = 'posts';
    protected $fillable = [
        'user_id',
        'slug',
        'title',
        'content',
        'draft',
    ];

    public function author()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}