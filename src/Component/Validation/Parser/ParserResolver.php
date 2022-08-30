<?php

declare(strict_types=1);

namespace Pandawa\Component\Validation\Parser;

use Pandawa\Contracts\Validation\Parser\ParserInterface;
use Pandawa\Contracts\Validation\Parser\ParserResolverInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ParserResolver implements ParserResolverInterface
{
    /**
     * @var ParserInterface[]
     */
    private array $parsers = [];

    /**
     * @param ParserInterface[] $parsers
     */
    public function __construct(iterable $parsers)
    {
        foreach ($parsers as $parser) {
            $this->addParser($parser);
        }
    }

    public function addParser(ParserInterface $parser): void
    {
        $this->parsers[] = $parser;
    }

    public function resolve(string $value): ?ParserInterface
    {
        foreach ($this->parsers as $parser) {
            if ($parser->supports($value)) {
                return $parser;
            }
        }

        return null;
    }
}
