<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Config\Parser;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface ParserResolverInterface
{
    public function resolve(mixed $value): ?ParserInterface;
}
