<?php
/**
 * This file is part of the pandawa package.
 *
 * (c) 2018 Pandawa <https://github.com/bl4ckbon3/pandawa>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    'controllers'     => [
        'invokable' => env('API_INVOKABLE_CONTROLLER', Pandawa\Module\Api\Http\Controller\InvokableController::class),
        'resource'  => env('API_RESOURCE_CONTROLLER', Pandawa\Module\Api\Http\Controller\ResourceController::class),
        'presenter' => env('API_PRESENTER_HANDLER', Pandawa\Module\Api\Http\Controller\PresenterHandler::class),
    ],
    'show_hostname'   => env('API_SHOW_HOSTNAME', true),
    'default_version' => env('API_DEFAULT_VERSION'),
    'auth'            => [
        'default' => 'jwt',

        'jwt' => [
            'algo' => env('AUTH_API_JWT_ALGO', 'RS512'),
            'keys' => [
                'rs' => [
                    'private_key' => env('AUTH_API_JWT_PRIVATE_KEY', storage_path('jwt/private.pem')),
                    'public_key'  => env('AUTH_API_JWT_PUBLIC_KEY', storage_path('jwt/public.pem')),
                    'passphrase'  => env('AUTH_API_JWT_PRIVATE_KEY_PHRASE', ''),
                ],
                'hs' => [
                    'secret_key' => env('AUTH_API_JWT_HASH_SECRET', ''),
                ],
            ],
            'ttl'  => env('AUTH_API_JWT_TTL'),
        ],
    ],
    'renderer' => Pandawa\Module\Api\Renderer\JsonApiRenderer::class,
    'default_transformers' => [
        Pandawa\Component\Transformer\ArrayableTransformer::class,
        Pandawa\Component\Transformer\ModelTransformer::class,
    ]
];
