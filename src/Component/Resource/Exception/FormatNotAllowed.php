<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource\Exception;

use Exception;
use Throwable;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class FormatNotAllowed extends Exception
{
    public function __construct(string $format, int $code = 400, Throwable $previous = null)
    {
        parent::__construct(
            sprintf('Format "%s" is not allowed.', $format),
            $code,
            $previous
        );
    }
}
