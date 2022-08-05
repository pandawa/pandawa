<?php

declare(strict_types=1);

namespace Pandawa\Component\Eloquent\Event;

use Illuminate\Database\Eloquent\Model;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ModelSaved
{
    public function __construct(public readonly Model $model)
    {
    }
}
