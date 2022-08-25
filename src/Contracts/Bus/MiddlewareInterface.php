<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Bus;

use Closure;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface MiddlewareInterface
{
    public function handle(Envelope $envelope, Closure $next): mixed;
}
