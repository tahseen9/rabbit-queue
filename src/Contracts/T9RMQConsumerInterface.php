<?php

namespace Tahseen9\RabbitQueue\Contracts;

use Closure;
use Exception;
use Tahseen9\RabbitQueue\Exceptions\Stop;
use PhpAmqpLib\Message\AMQPMessage;

interface T9RMQConsumerInterface
{

    /**
     * @throws Exception
     */
    public function connect(): self;

    /**
     * @param string $exchange
     * @return self
     */
    public function setExchange(string $exchange): self;

    /**
     * @param string $queue
     * @return self
     */
    public function onQueue(string $queue): self;

    /**
     * @param Closure $closure (Array $message, AmqpConsumerInterface $handler)
     * @param string|null $queue Queue name if not provided with method chain
     * @return void
     * @throws Exception
     */
    public function listen(Closure $closure, string $queue = null): void;

    /**
     * Acknowledges a message
     *
     * @param AMQPMessage|null $message
     */
    public function ack(AMQPMessage $message = null): void;

    /**
     * Rejects a message and re-queues it if wanted (default: false)
     *
     * @param bool $requeue
     * @param AMQPMessage|null $message
     */
    public function reject(bool $requeue = false, AMQPMessage $message = null): void;

    /**
     * Stops consumer when no message is left
     *
     * @throws Stop
     */
    public function stopWhenProcessed(): void;

    /**
     * @param bool $val
     * @return self
     */
    public function jsonArray(bool|null $val = null): self;

}
