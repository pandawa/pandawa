<?php

declare(strict_types=1);

namespace Pandawa\Bundle\AuthBundle\Http\Middleware;

use Illuminate\Auth\Middleware\Authorize;
use Pandawa\Annotations\Routing\AsMiddleware;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
#[AsMiddleware('can')]
class AuthorizeMiddleware extends Authorize
{
}
