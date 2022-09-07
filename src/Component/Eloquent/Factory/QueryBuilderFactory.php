<?php

declare(strict_types=1);

namespace Pandawa\Component\Eloquent\Factory;

use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Pandawa\Component\Eloquent\QueryBuilder;
use Pandawa\Contracts\Eloquent\Factory\CacheHandlerFactoryInterface;
use Pandawa\Contracts\Eloquent\Factory\QueryBuilderFactoryInterface;
use Pandawa\Contracts\Eloquent\QueryBuilderInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class QueryBuilderFactory implements QueryBuilderFactoryInterface
{
    public function __construct(
        protected readonly Container $container,
        protected readonly CacheHandlerFactoryInterface $cacheHandlerFactory,
    ) {
    }

    public function create(string $modelClass): QueryBuilderInterface
    {
        return $this->makeQueryBuilder()->setModel($this->createModel($modelClass));
    }

    protected function makeQueryBuilder(): QueryBuilderInterface
    {
        return $this->container->make(QueryBuilder::class, [
            'cacheHandler' => $this->cacheHandlerFactory->create(),
        ]);
    }

    protected function createModel(string $modelClass): Model
    {
        return new $modelClass();
    }
}
