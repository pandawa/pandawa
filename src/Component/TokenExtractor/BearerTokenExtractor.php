<?php

declare(strict_types=1);

namespace Pandawa\Component\TokenExtractor;

use Pandawa\Contracts\TokenExtractor\TokenExtractorInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class BearerTokenExtractor implements TokenExtractorInterface
{
    public function extract(ServerRequestInterface $request): ?string
    {
        $header = $request->getHeaderLine('Authorization');

        $position = strrpos($header, 'Bearer ');

        if (false !== $position) {
            $header = substr($header, $position + 7);

            return str_contains($header, ',') ? strstr($header, ',', true) : $header;
        }

        return null;
    }
}
