<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Eloquent\Cache;

use Illuminate\Database\Eloquent\Model;
use Pandawa\Contracts\Eloquent\QueryBuilderInterface;

/**
 * @template TModel
 *
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface CacheHandlerInterface
{
    /**
     * Cache model by primary key.
     *
     * @param  TModel  $model
     *
     * @return TModel
     */
    public function rememberModel(Model $model): Model;

    /**
     * @param  QueryBuilderInterface  $queryBuilder
     * @param  mixed  $value
     *
     * @return mixed
     */
    public function rememberQuery(QueryBuilderInterface $queryBuilder, mixed $value): mixed;

    /**
     * Get cache by key.
     */
    public function getByKey(Model $model, int|string $key): ?Model;

    /**
     * Get value by query.
     *
     * @param  QueryBuilderInterface  $queryBuilder
     *
     * @return mixed
     */
    public function getByQuery(QueryBuilderInterface $queryBuilder): mixed;

    /**
     * Invalidate cache by model.
     *
     * @param  TModel  $model
     *
     * @return TModel
     */
    public function invalidate(Model $model): Model;
}
