<?php
/**
 * This file is part of the Pandawa package.
 *
 * (c) 2018 Pandawa <https://github.com/bl4ckbon3/pandawa>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Pandawa\Module\Api\Security\TokenExtractor;

use Illuminate\Http\Request;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ChainTokenExtractor implements TokenExtractorInterface
{
    /**
     * @var TokenExtractorInterface[]
     */
    private $extractors = [];

    public function __construct(array $tokenExtractors)
    {
        foreach ($tokenExtractors as $tokenExtractor) {
            $this->add($tokenExtractor);
        }
    }

    public function add(TokenExtractorInterface $tokenExtractor): void
    {
        $this->extractors[] = $tokenExtractor;
    }

    public function extract(Request $request): ?string
    {
        foreach ($this->extractors as $tokenExtractor) {
            if ($token = $tokenExtractor->extract($request)) {
                return $token;
            }
        }

        return null;
    }
}
