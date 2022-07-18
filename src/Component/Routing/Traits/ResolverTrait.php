<?php

declare(strict_types=1);

namespace Pandawa\Component\Routing\Traits;

use Pandawa\Contracts\Routing\LoaderResolverInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait ResolverTrait
{
    protected LoaderResolverInterface $resolver;

    public function setResolver(LoaderResolverInterface $resolver): void
    {
        $this->resolver = $resolver;
    }
}
