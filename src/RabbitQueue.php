<?php

namespace Tahseen9\RabbitQueue;

use Closure;
use Exception;
use Tahseen9\RabbitQueue\Services\T9RmqConsumer;
use Tahseen9\RabbitQueue\Services\T9RmqProducer;

class RabbitQueue
{
    // Build wonderful things

    public function producer(): T9RmqProducer
    {
        return new T9RmqProducer();
    }

    public function consumer(): T9RmqConsumer
    {
        return new T9RmqConsumer();
    }

    /**
     * @throws Exception
     */
    public function dispatch(array $message, $queue_name): void {
        $this->producer()->dispatch($message, $queue_name);
    }

    /**
     * @param Closure $closure : $message, $handler
     * @throws Exception
     */
    public function listen(Closure $closure, string $queue_name): void {
        $this->consumer()->listen($closure, $queue_name);
    }
}
