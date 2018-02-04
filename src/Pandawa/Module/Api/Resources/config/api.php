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
    ],
    'show_hostname'   => env('API_SHOW_HOSTNAME', true),
    'default_version' => env('API_DEFAULT_VERSION'),
];
