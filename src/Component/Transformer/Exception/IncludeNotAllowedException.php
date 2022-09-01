<?php

declare(strict_types=1);

namespace Pandawa\Component\Transformer\Exception;

use Exception;
use Throwable;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class IncludeNotAllowedException extends Exception
{
    public function __construct(string $include, ?int $code = null, ?Throwable $previous = null)
    {
        parent::__construct(sprintf('Include "%s" is not allowed', $include), $code, $previous);
    }
}
