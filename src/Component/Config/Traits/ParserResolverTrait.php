<?php

declare(strict_types=1);

namespace Pandawa\Component\Config\Traits;

use Pandawa\Contracts\Config\Parser\ParserResolverInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait ParserResolverTrait
{
    protected ParserResolverInterface $resolver;

    public function setResolver(ParserResolverInterface $resolver): void
    {
        $this->resolver = $resolver;
    }
}
