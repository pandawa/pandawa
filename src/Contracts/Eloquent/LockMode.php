<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Eloquent;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
enum LockMode
{
    case PESSIMISTIC_WRITE;
    case PESSIMISTIC_READ;
}
