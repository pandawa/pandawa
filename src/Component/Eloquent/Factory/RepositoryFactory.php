<?php

declare(strict_types=1);

namespace Pandawa\Component\Eloquent\Factory;

use Illuminate\Contracts\Container\Container;
use Pandawa\Component\Eloquent\Repository;
use Pandawa\Contracts\Eloquent\Factory\QueryBuilderFactoryInterface;
use Pandawa\Contracts\Eloquent\Factory\RepositoryFactoryInterface;
use Pandawa\Contracts\Eloquent\Persistent\PersistentInterface;
use Pandawa\Contracts\Eloquent\RepositoryInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class RepositoryFactory implements RepositoryFactoryInterface
{
    public function __construct(
        protected readonly QueryBuilderFactoryInterface $queryBuilderFactory,
        protected readonly PersistentInterface $persistent,
        protected readonly Container $container,
    ) {
    }

    public function create(string $modelClass, string $repositoryClass = Repository::class): RepositoryInterface
    {
        $repositoryService = sprintf('Eloquent.%sRepository', $modelClass);

        if ($this->container->has($repositoryService)) {
            return $this->container->get($repositoryService);
        }

        return new $repositoryClass(
            $this->queryBuilderFactory->create($modelClass),
            $this->persistent,
        );
    }
}
