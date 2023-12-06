<?php

namespace Tahseen9\RabbitQueue\Services;

use Exception;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class T9RmqConnection
{
    /**
     * @throws Exception
     */
    public function getConnection(): AMQPStreamConnection
    {
        return new AMQPStreamConnection(
            config('rabbit-queue.host'),
            config('rabbit-queue.port'),
            config('rabbit-queue.username'),
            config('rabbit-queue.password'),
        );
    }
}
