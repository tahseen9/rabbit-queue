<?php

namespace Tahseen9\RabbitQueue\Contracts;

use Exception;

interface T9RMQProducerInterface
{

    /**
     * @return self
     * @throws Exception
     *
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
     * @param array $message
     * @param $queue
     * @return void
     * @throws Exception
     */
    public function dispatch(array $message, $queue = null): void;
}
