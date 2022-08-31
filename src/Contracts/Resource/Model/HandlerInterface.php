<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Resource\Model;

use Pandawa\Contracts\Transformer\TransformerInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface HandlerInterface
{
    /**
     * Set model class to handler.
     */
    public function setModel(string $model): static;

    /**
     * Get model key.
     */
    public function getModelKey(): string;

    /**
     * Store new model with given data.
     */
    public function store(array $data): object;

    /**
     * Update model with given id and data
     */
    public function update(string|int $id, array $data): object;

    /**
     * Delete model with given id
     */
    public function delete(string|int $id): object;

    /**
     * Find model by given id
     */
    public function findById(string|int $id): object;

    /**
     * Find collection of models
     */
    public function find(array $options = []): iterable;

    /**
     * @template TModel
     *
     * @param TModel $resource
     * @param array $relations
     *
     * @return TModel
     */
    public function loadRelations(object $resource, array $relations): object;

    /**
     * Add eager load relations.
     */
    public function withEager(array $relations): static;

    /**
     * Add criteria.
     */
    public function withCriteria(array $criteria): static;

    /**
     * Add filter based on array.
     */
    public function withFilter(array $criteria): static;

    /**
     * Return the repository.
     */
    public function getRepository(): object;

    /**
     * @return TransformerInterface
     */
    public function getDefaultTransformer(): TransformerInterface;
}
