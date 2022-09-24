<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Event;

use Illuminate\Contracts\Events\Dispatcher;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface EventBusInterface extends Dispatcher
{
    public function mergeMiddlewares(array $middlewares): void;

    public function fire(object|string $event, mixed $payload = []): mixed;
}
