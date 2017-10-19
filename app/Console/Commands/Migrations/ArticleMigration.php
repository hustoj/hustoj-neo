<?php

namespace App\Console\Commands\Migrations;

use App\Entities\Post;
use App\Services\UserService;

class ArticleMigration extends Migration
{
    public function handle($command)
    {
        $command->info('Migrating Articles...');
        $this->table('news')->orderBy('news_id', 'asc')->chunk(100, function ($objects) {
            $objects->map(function ($object) {
                $this->transformArticle($object);
            });
        });
        $command->info('Migrating Articles Done');
    }

    private function transformArticle($object)
    {
        $post = new Post();
        $post->title = $object->title;
        $post->content = $object->content;
        $author = app(UserService::class)->findByName($object->user_id);
        $post->user_id = $author->id;
        $post->created_at = $object->time;
        $post->priority = $object->importance;
        $post->save();
    }
}
