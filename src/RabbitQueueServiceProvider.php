<?php

namespace Tahseen9\RabbitQueue;

use Illuminate\Support\ServiceProvider;
use Tahseen9\RabbitQueue\Contracts\T9RMQConsumerInterface;
use Tahseen9\RabbitQueue\Services\T9RmqConsumer;
use Tahseen9\RabbitQueue\Services\T9RmqProducer;

class RabbitQueueServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/rabbit-queue.php', 'rabbit-queue');

        // Register the service the package provides.
        $this->app->singleton('rabbit-queue', function ($app) {
            return new RabbitQueue;
        });

        $this->app->bind(T9RMQConsumerInterface::class, T9RmqConsumer::class);

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['rabbit-queue'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/rabbit-queue.php' => config_path('rabbit-queue.php'),
        ], 'rabbit-queue.config');

        // Registering package commands.
        // $this->commands([]);
    }
}
