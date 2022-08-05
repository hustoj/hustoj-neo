<?php

namespace App\Observers;

use App\Entities\Reply;
use App\Entities\Topic;
use App\Entities\User;

class UserDeletedObserver
{
    public function handle(User $user)
    {
        $this->cleanRelatedTopic($user);
        $this->cleanRelatedReplies($user);
    }

    /**
     * @param  User  $user
     */
    public function cleanRelatedTopic(User $user): void
    {
        /** @var Topic[] $topics */
        logger()->info("user {$user->id} deleted, remove related topics");
        $topics = Topic::query()->where('user_id', $user->id)->get();
        foreach ($topics as $topic) {
            try {
                $topic->replies()->delete();
                $topic->delete();
            } catch (\Exception $e) {
                app('log')->error("remove topic failed! {$e->getMessage()}");
            }
        }
    }

    private function cleanRelatedReplies(User $user)
    {
        $replies = Reply::query()->where('user_id', $user->id)->get();
        $replies->each(function (Reply $reply) {
            $reply->delete();
        });
    }
}
