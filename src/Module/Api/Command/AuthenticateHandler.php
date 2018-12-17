<?php
declare(strict_types=1);

namespace Pandawa\Module\Api\Command;

use Illuminate\Auth\AuthManager;
use Pandawa\Module\Api\Security\Authentication\AuthenticationManager;
use Pandawa\Module\Api\Security\Model\Signature;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class AuthenticateHandler
{
    /**
     * @var AuthenticationManager
     */
    private $authManager;

    /**
     * @var AuthManager
     */
    private $laravelAuth;

    /**
     * Constructor.
     *
     * @param AuthenticationManager $authManager
     * @param AuthManager           $laravelAuth
     */
    public function __construct(AuthenticationManager $authManager, AuthManager $laravelAuth)
    {
        $this->authManager = $authManager;
        $this->laravelAuth = $laravelAuth;
    }

    /**
     * @param Authenticate $authenticate
     *
     * @return Signature
     */
    public function handle(Authenticate $authenticate)
    {
        $credentials = $authenticate->origin()->all();
        $guard = $this->laravelAuth->guard('api');

        if (false === $guard->validate($credentials)) {
            abort(401, 'The given credentials is invalid.');
        }

        $authenticator = (string)config('modules.api.auth.default');

        return $this->authManager->sign($authenticator, $guard->user());
    }
}
