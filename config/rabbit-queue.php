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
