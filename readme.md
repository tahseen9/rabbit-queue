# Rabbit Queue (Laravel Package)

[![Latest Version on Packagist][ico-version]][link-packagist]

A simple and elegant <a href="https://laravel.com" target="_blank">Laravel<a/> wrapper around <a href="https://github.com/php-amqplib/php-amqplib" target="_blank">php-amqplib</a> for dispatching to and listening from <a href="https://www.rabbitmq.com/" target="_blank">rabbitmq</a> queues.
### Avoiding High Connection Churn
Use <a href="https://laravel.com" target="_blank">AMQProxy<a/>, which is a proxy library with connection and channel pooling/reusing. This allows for lower connection and channel churn when using php-amqplib, leading to less CPU usage of RabbitMQ. 

## Installation

#### Via Dependency Composer

```bash
composer require tahseen9/rabbit-queue
```
###

#### Publish assets (config file)
```bash
php artisan vendor:publish --provider="Tahseen9\RabbitQueue\RabbitQueueServiceProvider"
```

## Usage

### Dispatch Queue
#### Via Dependency Injection
```bash
use Tahseen9\RabbitQueue\Contracts\T9RMQProducerInterface;

class MyProducer {
    
    private string $exchange = 'my_exchange';

    private string $queue = 'my_queue';
    
    private T9RMQConsumerInterface $producer;
    
    public function __construct(T9RMQProducerInterface $producer){
        # you can define the exchange and queue here
        $this->producer = $producer->setExchange($this->producer)
                                   ->onQueue($this->queue); 
       # if you are running multiple queues,
       # pass the queue name in second argument of dispatch method like shown in the facade section
    }
    
    public function fooProducer() {
        
        $this-producer->dispatch([
          'lang' => 'php',
          'framework' => 'laravel',
        ]); # return type void
    
    } # fooProducer end
} # class end
```
###
#### Via Facade
```bash
use Tahseen9\RabbitQueue\Facades\RabbitQueue;
...

RabbitQueue::dispatch(
    [
      'lang' => 'php',
      'framework' => 'laravel',
    ], # Message Array
    $queue_name = "my_queue" # Queue Name
 ); # return type void
```
###
### Listen Queue

#### Via Dependency Injection
```bash
use Tahseen9\RabbitQueue\Contracts\T9RMQConsumerInterface;

class MyListener {
    
    private string $exchange = 'my_exchange';

    private string $queue = 'my_queue';
    
    private T9RMQConsumerInterface $consumer;
    
    public function __construct(T9RMQConsumerInterface $consumer){
        
        # you can define the exchange and queue explicitly here
        $this->consumer = $consumer->setExchange($this->exchange)
                                   ->onQueue($this->queue); 
       # if you are running multiple queues,
       # pass the queue name in second argument of listen method like shown in the facade section
    
    }
    
    public function barListener() {
        
        $this-consumer->jsonArray(true) # want json array, set true, default null : used json_encode() inside
        ->listen(function($message, $handler){
        
            echo $message['lang']; # php
            echo $message['framework']; # laravel
            
            $handler->ack(); # acknowledge message after using it, so it will be removed from the queue
            
    #        $handler->stopWhenProcessed(); # use this if you want to stop execution after listening the whole queue    
    
          }); # return type void
          
    } # barListener end
} # class end
```
###
#### Via Facade
```bash
use Tahseen9\RabbitQueue\Facades\RabbitQueue;
...

RabbitQueue::listen(function($message, $handler){
    
    echo $message->lang; # php
    echo $message->framework; # laravel
    
    $handler->ack(); # acknowledge message after using it, so it will be removed from the queue
    
#    $handler->stopWhenProcessed(); # use this if you want to stop execution after listening the whole queue    

}, $queue_name = "my_queue"); # return type void
```
###
### Using via Class Instance
```bash
 $rabbitQueue = new \Tahseen9\RabbitQueue\RabbitQueue();
 
 #available methods:
 $rabbitQueue->producer(); # returns producer instance
 $rabbitQueue->consumer(); # returns consumer instance
 
 # by default queue_name is nullable you can skip this param if set via method e.g.
 # $rabbitQueue->producer()->onQueue('my_queue')->dispatch($msg);
 # $rabbitQueue->consumer()->onQueue('my_queue')->listen(Closure $closure);
 
 $rabbitQueue->dispatch(array $message, string $queueName); # dispatch queue directly on default exchange
 $rabbitQueue->listen(Closure $closure, string $queueName); # start listening instantly on default exchange
```
###
### Configuration file (rabbit-queue.php)
```bash
#publish file with this command:
php artisan vendor:publish --provider="Tahseen9\RabbitQueue\RabbitQueueServiceProvider"

<?php

return [
    # Connection - Set these in your env
    "host" => env("RABBITMQ_HOST", "localhost"),
    "port" => env("RABBITMQ_PORT", "5672"),
    "username" => env("RABBITMQ_USERNAME", "guest"),
    "password" => env("RABBITMQ_PASSWORD", "guest"),

    # define exchange name or use method to define if dealing with multiple exchanges
    "exchange" => env("EXCHANGE_NAME", env("APP_NAME", "LARAVEL_RABBIT_EXCHANGE")),
    "exchange_type" => env("EXCHANGE_TYPE", "direct"), # this option is only available via env for now
    "exchange_passive" => false,
    "exchange_durable" => true, # persistent exchange
    "exchange_auto_delete" => false,

    "routing_key_postfix" => env("ROUTING_KEY_POSTFIX", "_key"),
    "consumer_tag_post_fix" => env("ROUTING_KEY_POSTFIX", "_tag"),

    "qos" => true, # this will apply prefetch count and prefetch size

    # These will work if qos is true
    "qos_prefetch_size" => 0, # unlimited multiple of prefetch count
    "qos_prefetch_count" => 1, # process 1 job by 1 worker at a time, increasing this number will pre load x amount of jobs in memory for worker
    "qos_a_global" => false,

    # Queue Declaration
    "queue_passive" => false,
    "queue_durable" => true, # persistent queue
    "queue_exclusive" => false,
    "queue_auto_delete" => false,

    # Consumer declaration
    "consumer_no_local" => false,
    "consumer_no_ack" => false, # default: must acknowledge else true
    "consumer_exclusive" => false,
    "consumer_no_wait" => false,

    "message_delivery_mode" => 2 // DELIVERY MODE PERSISTENT = 2 | DELIVERY MODE NON PERSISTENT = 1
];
```
## Change log

Version 1.0.0 Released

## Security

If you discover any security related issues, please email tehzu9@hotmail.com instead of using the issue tracker.

## Credits

- AMQProxy team
- php-amqplib team

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/tahseen9/rabbit-queue.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/tahseen9/rabbit-queue.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/tahseen9/rabbit-queue/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/tahseen9/rabbit-queue
[link-downloads]: https://packagist.org/packages/tahseen9/rabbit-queue
[link-travis]: https://travis-ci.org/tahseen9/rabbit-queue
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/tahseen9
[link-contributors]: ../../contributors
