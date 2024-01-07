<?php

declare(strict_types=1);

namespace Pandawa\Component\TokenExtractor;

use Pandawa\Contracts\TokenExtractor\TokenExtractorInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class QueryParamTokenExtractor implements TokenExtractorInterface
{
    public static string $PARAM = '_token';

    public function extract(ServerRequestInterface $request): ?string
    {
        return $request->getQueryParams()[self::$PARAM] ?? null;
    }
}
