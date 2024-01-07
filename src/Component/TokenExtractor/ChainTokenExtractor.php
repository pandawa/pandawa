<?php

declare(strict_types=1);

namespace Pandawa\Component\TokenExtractor;

use Pandawa\Contracts\TokenExtractor\TokenExtractorInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ChainTokenExtractor implements TokenExtractorInterface
{
    /**
     * @var TokenExtractorInterface[]
     */
    private array $tokenExtractors;

    public function __construct()
    {
        $this->tokenExtractors = [
            new BearerTokenExtractor(),
            new QueryParamTokenExtractor(),
        ];
    }

    public function extract(ServerRequestInterface $request): ?string
    {
        foreach ($this->tokenExtractors as $tokenExtractor) {
            if (null !== $token = $tokenExtractor->extract($request)) {
                return $token;
            }
        }

        return null;
    }
}
