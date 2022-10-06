<?php

declare(strict_types=1);

namespace Pandawa\Component\Event\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Collection;
use Pandawa\Component\Eloquent\Model;
use Pandawa\Contracts\Eloquent\Action\Action;
use Pandawa\Contracts\Eloquent\Persistent\MiddlewareInterface;
use Pandawa\Contracts\Event\EventBusInterface;
use Pandawa\Contracts\Event\HasDomainEventInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ReleasePendingDomainEvents implements MiddlewareInterface
{
    public function __construct(protected readonly EventBusInterface $eventBus)
    {
    }

    public function handle(Action $action, Closure $next): mixed
    {
        return tap($next($action), function (Model $model) {
            $this->releaseDomainEvents($model);
        });
    }

    protected function releaseDomainEvents(Model $model): void
    {
        if ($model instanceof HasDomainEventInterface) {
            foreach ($model->releaseDomainEvents() as $event) {
                $this->eventBus->fire($event);
            }
        }

        foreach ($model->getRelations() as $models) {
            $models = $models instanceof Collection
                ? $models->all() : [$models];

            foreach (array_filter($models) as $model) {
                $this->releaseDomainEvents($model);
            }
        }
    }
}
