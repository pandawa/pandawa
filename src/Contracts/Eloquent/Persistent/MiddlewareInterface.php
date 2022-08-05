<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Eloquent\Persistent;

use Closure;
use Pandawa\Contracts\Eloquent\Action\Action;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface MiddlewareInterface
{
    public function handle(Action $action, Closure $next): mixed;
}
