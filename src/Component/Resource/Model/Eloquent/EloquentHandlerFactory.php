<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource\Model\Eloquent;

use Illuminate\Contracts\Container\Container;
use Pandawa\Component\Eloquent\Model;
use Pandawa\Contracts\Resource\Model\FactoryInterface;
use Pandawa\Contracts\Resource\Model\HandlerInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class EloquentHandlerFactory implements FactoryInterface
{
    protected Container $container;

    public function setContainer(Container $container): static
    {
        $this->container = $container;

        return $this;
    }

    public function create(string $model): HandlerInterface
    {
        return $this->container->make(EloquentHandler::class)->setModel($model);
    }

    public function supports(string $model): bool
    {
        return is_subclass_of($model, Model::class);
    }
}
