<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Validation\Parser;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface ParserResolverInterface
{
    /**
     * Add a parser to resolver.
     */
    public function addParser(ParserInterface $parser): void;

    /**
     * Resolve parser for given value.
     */
    public function resolve(string $value): ?ParserInterface;
}
