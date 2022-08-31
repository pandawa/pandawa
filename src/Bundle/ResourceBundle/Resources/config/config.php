<?php

use Pandawa\Component\Resource;

return [
    'default_wrapper' => 'data',

    'default_content_type' => 'application/json',

    'allowed_formats' => [
        'json',
        'xml',
    ],

    'middlewares' => [
        Resource\Middleware\AddClientIpMiddleware::class,
        Resource\Middleware\AddHostnameMiddleware::class,
        Resource\Middleware\AddVersionMiddleware::class,
    ],

    'controller' => [
        'resource' => Resource\Http\Controller\ResourceController::class,
        'message' => Resource\Http\Controller\MessageController::class,
    ],

];
