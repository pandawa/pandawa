<?php

use Illuminate\Support\Str;

return [
    'default' => env('REDIS_CONNECTION', 'default'),
    'client'  => env('REDIS_CLIENT', 'phpredis'),

    'options' => [
        'cluster' => env('REDIS_CLUSTER', 'redis'),
        'prefix'  => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'pandawa'), '_').'_database_'),
    ],

    'connections' => [
        'default' => [
            'url'      => env('REDIS_URL'),
            'host'     => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port'     => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url'      => env('REDIS_CACHE_URL', env('REDIS_URL')),
            'host'     => env('REDIS_CACHE_HOST', env('REDIS_HOST')),
            'username' => env('REDIS_CACHE_USERNAME', env('REDIS_USERNAME')),
            'password' => env('REDIS_CACHE_PASSWORD', env('REDIS_PASSWORD')),
            'port'     => env('REDIS_CACHE_PORT', env('REDIS_PORT')),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],
    ],

];
