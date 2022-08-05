<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Eloquent\Action;

use Illuminate\Database\Eloquent\Model;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Action
{
    public function __construct(public readonly Model $model, public readonly Type $type)
    {
    }

    public static function save(Model $model): self
    {
        return new self($model, Type::SAVE);
    }

    public static function delete(Model $model): self
    {
        return new self($model, Type::DELETE);
    }
}
