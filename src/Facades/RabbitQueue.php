<?php

namespace Tahseen9\RabbitQueue\Facades;

use Illuminate\Support\Facades\Facade;
use Closure;
/**
 * @method static producer(): AmqpProducerService
 * @method static consumer(): AmqpConsumerService
 * @method static dispatch(array $message, string $queue_name): void
 * @method static listen(Closure $closure, string $queue_name): void
 */
class RabbitQueue extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \Tahseen9\RabbitQueue\RabbitQueue::class;
    }
}
