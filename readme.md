# Rabbit Queue (Laravel Package)

[![Latest Version on Packagist][ico-version]][link-packagist]

A simple and elegant <a href="https://laravel.com" target="_blank">Laravel<a/> wrapper around <a href="https://github.com/php-amqplib/php-amqplib" target="_blank">php-amqplib</a> for dispatching to and listening from <a href="https://www.rabbitmq.com/" target="_blank">rabbitmq</a> queues.
### Avoiding High Connection Churn
Use <a href="https://laravel.com" target="_blank">AMQProxy<a/>, which is a proxy library with connection and channel pooling/reusing. This allows for lower connection and channel churn when using php-amqplib, leading to less CPU usage of RabbitMQ. 

## Installation

Via Composer

```bash
composer require tahseen9/rabbit-queue
```
then publish the config file
```bash
php artisan vendor:publish --provider="Tahseen9\RabbitQueue\RabbitQueueServiceProvider"
```

## Usage

### Dispatch Queue

```bash
use Tahseen9\RabbitQueue\Facades\RabbitQueue;
...

RabbitQueue::dispatch(
    [
      'lang' => 'php',
      'framework' => 'laravel',
    ], # Message Array
    $queue_name = "my_queue" # Queue Name
 );
```

### Listen Queue

```bash
use Tahseen9\RabbitQueue\Facades\RabbitQueue;
...

RabbitQueue::listen(function($message, $handler){
    
    echo $message->lang; # php
    
    echo $message->framework; # laravel
    
    $handler->ack(); # acknowledge message after using it, so it will be removed from the queue
    
    $handler->stopWhenProcessed(); # use this if you want to stop execution after listening the whole queue    

}, $queue_name = "my_queue");
```


## Change log

First Version 1.0.

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
