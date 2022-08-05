<?php

declare(strict_types=1);

namespace Pandawa\Component\Eloquent\Persistent;

use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pipeline\Pipeline;
use Pandawa\Contracts\Eloquent\Action\Action;
use Pandawa\Contracts\Eloquent\Persistent\MiddlewareInterface;
use Pandawa\Contracts\Eloquent\Persistent\PersistentInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class DatabasePersistent implements PersistentInterface
{
    /**
     * @var MiddlewareInterface[]
     */
    public array $middlewares = [];

    public function __construct(protected Container $container, array $middlewares = [])
    {
        foreach ($middlewares as $middleware) {
            if (is_string($middleware)) {
                $this->addMiddleware($this->container->make($middleware));

                continue;
            }

            $this->addMiddleware($middleware);
        }
    }

    public function addMiddleware(MiddlewareInterface $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    public function save(Model $model): Model
    {
        return $this->dispatchThroughMiddlewares(Action::save($model), function (Action $action) {
            $action->model->push();

            return $action->model->refresh();
        });
    }

    public function delete(Model $model): Model
    {
        return $this->dispatchThroughMiddlewares(Action::delete($model), function (Action $action) {
            $action->model->delete();

            return $action->model;
        });
    }

    protected function dispatchThroughMiddlewares(Action $action, callable $callable): mixed
    {
        return (new Pipeline())
            ->send($action)
            ->through($this->middlewares)
            ->then($callable);
    }
}
