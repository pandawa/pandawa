<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Eloquent\Action;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
enum Type
{
    case SAVE;
    case DELETE;

    public function is(Type $type): bool
    {
        return $this === $type;
    }
}
