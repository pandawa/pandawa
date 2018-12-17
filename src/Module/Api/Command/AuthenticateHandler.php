<?php
declare(strict_types=1);

namespace Pandawa\Module\Api\Command;

use Illuminate\Contracts\Auth\UserProvider;
use Pandawa\Module\Api\Security\Authentication\AuthenticationManager;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class AuthenticateHandler
{
    /**
     * @var UserProvider
     */
    private $provider;

    /**
     * @var AuthenticationManager
     */
    private $authManager;

    /**
     * Constructor.
     *
     * @param UserProvider          $provider
     * @param AuthenticationManager $authManager
     */
    public function __construct(UserProvider $provider, AuthenticationManager $authManager)
    {
        $this->provider = $provider;
        $this->authManager = $authManager;
    }

    public function handle(Authenticate $authenticate)
    {
        $credentials = $authenticate->origin()->all();
        $user = $this->provider->retrieveByCredentials($credentials);

        if (null === $user) {
            abort(401, 'There is no user found with given credentials.');
        }

        if (false === $this->provider->validateCredentials($user, $credentials)) {
            abort(401, 'The given credentials is invalid.');
        }

        $authenticator = (string)config('modules.api.auth.default');

        return $this->authManager->sign($authenticator, $user);
    }
}
