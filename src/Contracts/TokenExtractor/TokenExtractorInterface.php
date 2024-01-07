<?php

declare(strict_types=1);

namespace Pandawa\Contracts\TokenExtractor;

use Psr\Http\Message\ServerRequestInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface TokenExtractorInterface
{
    public function extract(ServerRequestInterface $request): ?string;
}
