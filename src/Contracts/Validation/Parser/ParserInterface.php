<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Validation\Parser;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface ParserInterface
{
    /**
     * Parse given value.
     */
    public function parse(string $value): mixed;

    /**
     * Check if given value support for parser.
     */
    public function supports(string $value): bool;
}
