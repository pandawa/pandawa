<?php

declare(strict_types=1);

namespace Pandawa\Component\Eloquent\Persistent\Middleware;

use Closure;
use Illuminate\Contracts\Events\Dispatcher;
use Pandawa\Component\Eloquent\Event\ModelDeleted;
use Pandawa\Component\Eloquent\Event\ModelSaved;
use Pandawa\Component\Eloquent\Model;
use Pandawa\Contracts\Eloquent\Action\Action;
use Pandawa\Contracts\Eloquent\Action\Type;
use Pandawa\Contracts\Eloquent\Persistent\MiddlewareInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class DispatchEvent implements MiddlewareInterface
{
    public function __construct(protected Dispatcher $eventDispatcher)
    {
    }

    public function handle(Action $action, Closure $next): mixed
    {
        return tap($next($action), function (Model $model) use ($action) {
            if (null !== $event = $this->createEvent($model, $action)) {
                $this->eventDispatcher->dispatch($event);
            }
        });
    }

    protected function createEvent(Model $model, Action $action): ModelSaved|ModelDeleted|null
    {
        if ($action->type->is(Type::SAVE)) {
            return new ModelSaved($model);
        }

        if ($action->type->is(Type::DELETE)) {
            return new ModelDeleted($model);
        }

        return null;
    }
}
