<?php

return [
    'host' => env('RABBITMQ_HOST', '127.0.0.1'),
    'port' => env('RABBITMQ_PORT', 5672),

    'vhost'    => env('RABBITMQ_VHOST', '/'),
    'login'    => env('RABBITMQ_LOGIN', 'guest'),
    'password' => env('RABBITMQ_PASSWORD', 'guest'),

    'queue' => env('RABBITMQ_QUEUE', 'hustoj'),

    'routing_key' => env('RABBITMQ_ROUTE_KEY', 'hustoj'),

    'options' => [

        'exchange' => [

            'name' => env('RABBITMQ_EXCHANGE_NAME'),

            /*
             * Determine if exchange should be created if it does not exist.
             */

            'declare' => env('RABBITMQ_EXCHANGE_DECLARE', true),

            /*
             * Read more about possible values at https://www.rabbitmq.com/tutorials/amqp-concepts.html
             */

            'type'        => env('RABBITMQ_EXCHANGE_TYPE'),
            'passive'     => env('RABBITMQ_EXCHANGE_PASSIVE', false),
            'durable'     => env('RABBITMQ_EXCHANGE_DURABLE', true),
            'auto_delete' => env('RABBITMQ_EXCHANGE_AUTODELETE', false),
            'arguments'   => env('RABBITMQ_EXCHANGE_ARGUMENTS'),
        ],
    ],
];
