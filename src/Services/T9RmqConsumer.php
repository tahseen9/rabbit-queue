<?php

namespace Tahseen9\RabbitQueue\Services;

use Tahseen9\RabbitQueue\Contracts\T9RMQConsumerInterface;
use Tahseen9\RabbitQueue\Exceptions\Stop;
use Closure;
use Exception;
use PhpAmqpLib\Channel\AbstractChannel;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;

class T9RmqConsumer implements T9RMQConsumerInterface
{
    private ?AMQPStreamConnection $connection;
    private AbstractChannel|AMQPChannel|null $channel;
    private string $exchange;
    private mixed $queue_info;
    private ?string $queue;
    private bool $qos;
    private int $message_count;
    private bool|null $json_array;
    private AMQPMessage $message;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->queue = null;
        $this->qos(config('rabbit-queue.qos'));
        $this->connection = null;
        $this->channel = null;
        $this->json_array = null;
    }

    public function connect(): self
    {
        $connection = new T9RmqConnection();
        $this->connection = $connection->getConnection();
        $this->channel = $this->connection->channel();
        $this->setExchange(config('rabbit-queue.exchange'));

        return $this;
    }

    // true for associative array, false for non associative array, default null
    public function jsonArray(bool|null $val = null): self {
        $this->json_array = $val;
        return $this;
    }

    public function setExchange(string $exchange): self
    {
        $this->exchange = $exchange;

        $this->channel->exchange_declare($exchange, config('rabbit-queue.exchange_type'), config('rabbit-queue.exchange_passive'), config('rabbit-queue.exchange_durable'), config('rabbit-queue.exchange_auto_delete'));

        return $this;
    }

    public function onQueue(string $queue): self
    {
        $this->queue = $queue;
        return $this;
    }

    public function qos(bool $qos): self
    {
        $this->qos = $qos;
        return $this;
    }

    public function listen(Closure $closure, string $queue = null): void
    {
        $qos = $this->qos;

        if ($this->queue){
            $queue = $this->queue;
        }

        if ($queue){
            $this->queue = $queue;
        }

        $queue ?? throw new Exception("Undefined Queue!");

        if(is_null($this->connection)){
            $this->connect();
        }

        $this->queue_info = $this->channel->queue_declare($queue, config('rabbit-queue.queue_passive'), config('rabbit-queue.queue_durable'), config('rabbit-queue.queue_exclusive'), config('rabbit-queue.queue_auto_delete'));
        $this->channel->queue_bind($queue, $this->exchange, $queue . config('rabbit-queue.routing_key_postfix'));

        try {
            $this->message_count = $this->getQueueMessageCount();


            $amqpCallback = function ($message) use ($closure) {
                $this->message = $message;
                $closure(json_decode($message->body, $this->json_array), $this);
            };

            if($qos){
                $this->channel->basic_qos(config('rabbit-queue.qos_prefetch_size'), config('rabbit-queue.qos_prefetch_count'), config('rabbit-queue.qos_a_global'));
            }

            $this->channel->basic_consume($queue, $queue . config('rabbit-queue.consumer_tag_post_fix'), config('rabbit-queue.consumer_no_local'), config('rabbit-queue.consumer_no_ack'), config('rabbit-queue.consumer_exclusive'), config('rabbit-queue.consumer_no_wait'), $amqpCallback);

            while (count($this->channel->callbacks)) {
                $this->channel->wait();
            }

        } catch (Exception $exception) {

            if ($exception instanceof Stop) {
                return;
            }

            if ($exception instanceof AMQPTimeoutException) {
                return;
            }

            throw $exception;

        }

    }

    public function ack(AMQPMessage $message = null): void
    {
        $message ?? $message = $this->message;
        $message->getChannel()->basic_ack($message->getDeliveryTag());

        if ($message->body === 'quit') {
            $message->getChannel()->basic_cancel($message->getConsumerTag());
        }
    }


    public function reject(bool $requeue = false,AMQPMessage $message = null): void
    {
        $message ?? $message = $this->message;

        $message->getChannel()->basic_reject($message->getDeliveryTag(), $requeue);
    }


    public function stopWhenProcessed(): void
    {
        if (--$this->message_count <= 0) {
            throw new Stop();
        }
    }

    /**
     * @return int
     */
    private function getQueueMessageCount(): int
    {
        if (is_array($this->queue_info)) {
            return $this->queue_info[1];
        }
        return 0;
    }

    /**
     * @throws Exception
     */
    private function shutdown(AMQPChannel $channel, AMQPStreamConnection $connection): void
    {
        if ($connection->isConnected()) {
            if ($channel->is_open()) {
                $channel->close();
            }
            $connection->close();
        }
    }

    public function __destruct()
    {
        if(!is_null($this->channel)){
            try {
                $this->shutdown($this->channel, $this->connection);
            } catch (Exception $e) {
                echo $e->getTraceAsString();
            }
        }
    }
}
