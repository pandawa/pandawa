<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Resource;

use Closure;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface RendererMiddlewareInterface
{
    public function handle(Rendering $rendering, Closure $next): array;
}
