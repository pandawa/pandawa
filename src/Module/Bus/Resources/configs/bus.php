<?php
/**
 * This file is part of the Pandawa package.
 *
 * (c) 2018 Pandawa <https://github.com/bl4ckbon3/pandawa>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    'enable_event_publisher' => true,
    'enable_db_transaction'  => true,
    'registry_class'         => Pandawa\Component\Message\MessageRegistry::class,
    'dispatcher_class'       => Pandawa\Component\Bus\Dispatcher::class,
];
