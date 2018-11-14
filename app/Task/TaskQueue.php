<?php

namespace App\Task;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class TaskQueue
{
    private $connection;
    private $channel;

    public function __construct()
    {
        $this->connection = $this->getConnection();
        $this->channel = $this->queueChannel();
    }

    public function add(Task $task)
    {
        $message = $this->makeMessage($task);
        $this->channel->basic_publish($message, '', config('rabbitmq.routing_key'));
    }

    public function close()
    {
        $this->channel->close();
        $this->connection->close();
    }

    private function getConnection()
    {
        return new AMQPStreamConnection(
            config('rabbitmq.host'),
            config('rabbitmq.port'),
            config('rabbitmq.login'),
            config('rabbitmq.password'),
            config('rabbitmq.vhost')
        );
    }

    private function queueChannel()
    {
        $channel = $this->connection->channel();
        $channel->queue_declare(config('rabbitmq.queue'), false, true);

        return $channel;
    }

    private function makeMessage(Task $task)
    {
        $content = json_encode($task->asQueueInfo());

        $message = new AMQPMessage();
        $message->setBody($content);

        return $message;
    }
}
