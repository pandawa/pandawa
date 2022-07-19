<?php

declare(strict_types=1);

namespace Pandawa\Component\Config\Parser;

use Pandawa\Contracts\Config\Parser\ParserInterface;
use Pandawa\Contracts\Config\Parser\ParserResolverInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ParserResolver implements ParserResolverInterface
{
    /**
     * @var ParserInterface[]
     */
    protected array $parsers = [];

    /**
     * @var ParserInterface[] $parsers
     */
    public function __construct(array $parsers)
    {
        foreach ($parsers as $parser) {
            $this->addParser($parser);
        }
    }

    public function addParser(ParserInterface $parser): void
    {
        $this->parsers[] = $parser;
        $parser->setResolver($this);
    }

    public function resolve(mixed $value): ?ParserInterface
    {
        foreach ($this->parsers as $parser) {
            if ($parser->supports($value)) {
                return $parser;
            }
        }

        return null;
    }
}
