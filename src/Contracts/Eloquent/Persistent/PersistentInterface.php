<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Eloquent\Persistent;

use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel
 *
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface PersistentInterface
{
    /**
     * Add middleware.
     */
    public function addMiddleware(MiddlewareInterface $middleware): void;

    /**
     * Save the model to persistent.
     *
     * @param  TModel  $model
     *
     * @return TModel
     */
    public function save(Model $model): Model;

    /**
     * Delete the model from persistent.
     *
     * @param  TModel  $model
     *
     * @return TModel
     */
    public function delete(Model $model): Model;
}
