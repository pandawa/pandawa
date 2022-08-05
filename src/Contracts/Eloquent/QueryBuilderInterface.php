<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Eloquent;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as ModelCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

/**
 * @template TModel
 *
 * @mixin Builder
 *
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface QueryBuilderInterface
{
    /**
     * Set the model to query builder.
     *
     * @param  TModel  $model
     *
     * @return static
     */
    public function setModel(Model $model): static;

    /**
     * Set cache enabled or disabled.
     *
     * @param  bool  $enabled
     *
     * @return static
     */
    public function enableCache(bool $enabled = true): static;

    /**
     * Apply criteria to query.
     *
     * @param  CriterionInterface|array  $criteria
     *
     * @return static
     */
    public function withCriteria(CriterionInterface|array $criteria): static;

    /**
     * Get the model.
     *
     * @return TModel
     */
    public function getModel(): Model;

    /**
     * @param  array  $columns
     *
     * @return Collection<TModel>|ModelCollection<TModel>
     */
    public function get(array $columns = ['*']): Collection|ModelCollection;

    /**
     * @return LazyCollection<TModel>
     */
    public function cursor(): LazyCollection;

    /**
     * Find the model by primary key.
     *
     * @param  int|string  $key
     *
     * @return TModel|null
     */
    public function findByKey(int|string $key): ?Model;

    /**
     * @param  array  $columns
     *
     * @return TModel|null
     */
    public function first(array $columns = ['*']): ?Model;
}
