<?php

namespace App\Console\Commands\Migrations;

use App\Entities\Reply;
use App\Entities\Topic;
use App\Services\UserService;

class TopicMigration extends Migration
{
    public function handle($command)
    {
        $command->info('Migrating Topic...');
        $this->table('topic')->orderBy('tid', 'asc')->chunk(100, function ($items) {
            foreach ($items as $item) {
                $this->transform($item);
            }
        });
        $command->info('Migrating Topic Done');
    }

    private function transform($item)
    {
        $topic = new Topic();
        $topic->id = $item->tid;
        $topic->title = $item->title;
        $topic->contest_id = $item->cid;
        $topic->problem_id = $item->pid;
        if (! $topic->contest_id) {
            $topic->contest_id = 0;
        }
        $user = app(UserService::class)->findByName($item->author_id);
        $topic->user_id = $user->id;
        $topic->save();

        $replies = $this->table('reply')->where('topic_id', $item->tid)->orderBy('rid', 'asc')->get();
        $isFirst = true;
        foreach ($replies as $reply) {
            if ($isFirst && $item->author_id === $reply->author_id) {
                $topic->content = $reply->content;
                $topic->created_at = $reply->time;
                $topic->save();
                $isFirst = false;
                continue;
            }
            $newReply = new Reply();
            $newReply->topic_id = $topic->id;
            $newReply->created_at = $reply->time;
            $newReply->content = $reply->content;
            $author = app(UserService::class)->findByName($reply->author_id);
            $newReply->user_id = $author->id;
            $newReply->save();
        }
    }
}
