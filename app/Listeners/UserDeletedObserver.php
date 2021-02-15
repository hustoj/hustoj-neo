<?php

namespace App\Listeners;

use App\Entities\Topic;
use App\Entities\User;
use App\Repositories\TopicRepository;

class UserDeletedObserver
{
    /**
     * @var TopicRepository
     */
    private $topicRepository;

    public function __construct()
    {
        $this->topicRepository = app(TopicRepository::class);
    }

    public function handle(User $user)
    {
        $this->cleanRelatedTopic($user);
    }

    /**
     * @param User $user
     */
    public function cleanRelatedTopic(User $user): void
    {
        /** @var Topic[] $topics */
        app('log')->info("user {$user->id} deleted, remove related topics");
        $topics = $this->topicRepository->findAllBy('user_id', $user->id);
        foreach ($topics as $topic) {
            try {
                $topic->replies()->delete();
                $topic->delete();
            } catch (\Exception $e) {
                app('log')->error("remove topic failed! {$e->getMessage()}");
            }
        }
    }
}
