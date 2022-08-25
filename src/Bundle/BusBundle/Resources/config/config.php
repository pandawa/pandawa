<?php

use Pandawa\Component\Bus;

return [
    /*
    |--------------------------------------------------------------------------
    | Middlewares
    |--------------------------------------------------------------------------
    |
    | This value is middlewares for message bus.
    |
    */
    'middlewares' => [
        Bus\Middleware\RunInDatabaseTransactionMiddleware::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Message Registry
    |--------------------------------------------------------------------------
    |
    | This message registry class is used to store mapped messages.
    |
    */
    'registry' => Bus\MessageRegistry::class,

    /*
    |--------------------------------------------------------------------------
    | Queue Factory
    |--------------------------------------------------------------------------
    |
    | Queue factory is used to create queue connection.
    |
    */
    'queue_factory' => Bus\Factory\QueueFactory::class,

    /*
    |--------------------------------------------------------------------------
    | Message Bus
    |--------------------------------------------------------------------------
    |
    | Message bus is used to dispatch any message.
    |
    */
    'message_bus' => Bus\MessageBus::class,

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    |
    | Here you may register your messages.
    |
    */
    'messages' => [],

    /*
    |--------------------------------------------------------------------------
    | Handlers
    |--------------------------------------------------------------------------
    |
    | This value is used to map handler for message.
    |
    */
    'handlers' => [],
];
