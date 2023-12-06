<?php

namespace Tahseen9\RabbitQueue\Services;

use Tahseen9\RabbitQueue\Contracts\T9RMQProducerInterface;
use Exception;
use InvalidArgumentException;
use PhpAmqpLib\Channel\AbstractChannel;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class T9RmqProducer implements T9RMQProducerInterface
{

    private ?AMQPStreamConnection $connection;
    private AbstractChannel|AMQPChannel $channel;
    private string $exchange;

    private ?string $queue;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->connection = null;

        $connection = new T9RmqConnection();
        $this->connection = $connection->getConnection();
        $this->connection->set_close_on_destruct();
        $this->queue = null;
    }
    public function connect(): self
    {
        $this->channel = $this->connection->channel();
        $this->setExchange(config('rabbit-queue.exchange'));

        return $this;
    }
    public function setExchange(string $exchange): self
    {
        $this->exchange = $exchange;
        $this->channel->exchange_declare($this->exchange, config('rabbit-queue.exchange_type'), config('rabbit-queue.exchange_passive'), config('rabbit-queue.exchange_durable'), config('rabbit-queue.exchange_auto_delete'));

        return $this;
    }
    public function onQueue(string $queue): T9RMQProducerInterface
    {
        $this->queue = $queue;
        return $this;
    }

    public function dispatch(array $message, $queue = null): void
    {
        $this->connect();

        if ($this->queue){
            $queue = $this->queue;
        }

        $queue ?? throw new Exception("Undefined Queue!");

        !empty($message) ?? throw new InvalidArgumentException("Message must be a non empty array!");
        $message = json_encode($message);

        $this->channel->queue_declare($queue, config('rabbit-queue.queue_passive'), config('rabbit-queue.queue_durable'),  config('rabbit-queue.queue_exclusive'),  config('rabbit-queue.queue_auto_delete'));
        $this->channel->queue_bind($queue, $this->exchange,$queue . config('rabbit-queue.routing_key_postfix'));

        $this->channel->basic_publish(new AMQPMessage($message, ['delivery_mode' => config('rabbit-queue.message_delivery_mode')]), $this->exchange,$queue . config('rabbit-queue.routing_key_postfix'));
        $this->channel->close();
    }

}
