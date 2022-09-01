<?php

declare(strict_types=1);

namespace Pandawa\Component\Transformer\Exception;

use Exception;
use Throwable;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class SelectNotAllowedException extends Exception
{
    public function __construct(string $select, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(sprintf('Select "%s" is not allowed', $select), $code, $previous);
    }
}
