<?php

declare(strict_types=1);

namespace Pandawa\Bundle\SessionBundle\Http\Middleware;

use Illuminate\Session\Middleware\AuthenticateSession as BaseAuthenticateSession;
use Pandawa\Annotations\Routing\AsMiddleware;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
#[AsMiddleware('auth.session')]
class AuthenticateSession extends BaseAuthenticateSession
{
}
