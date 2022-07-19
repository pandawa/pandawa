<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Config\Parser;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface ParserInterface
{
    public function setResolver(ParserResolverInterface $resolver): void;

    public function parse(mixed $value): mixed;

    public function supports(mixed $value): bool;
}
