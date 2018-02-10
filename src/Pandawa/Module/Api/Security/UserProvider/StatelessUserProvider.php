<?php
/**
 * This file is part of the Pandawa package.
 *
 * (c) 2018 Pandawa <https://github.com/bl4ckbon3/pandawa>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Pandawa\Module\Api\Security\UserProvider;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Pandawa\Module\Api\Security\Model\AuthenticatedUser;
use RuntimeException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class StatelessUserProvider implements UserProvider
{
    /**
     * @var string
     */
    private $model;

    public function __construct(string $model)
    {
        $this->model = $model;
    }

    /**
     * @param mixed $identifier
     *
     * @return AuthenticatedUser
     */
    public function retrieveById($identifier)
    {
        $class = $this->model;

        return new $class($identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function retrieveByToken($identifier, $token)
    {
        throw new RuntimeException('Unsupported called method "retrieveByToken"');
    }

    /**
     * {@inheritdoc}
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        throw new RuntimeException('Unsupported called method "updateRememberToken"');
    }

    /**
     * {@inheritdoc}
     */
    public function retrieveByCredentials(array $credentials)
    {
        throw new RuntimeException('Unsupported called method "retrieveByCredentials"');
    }

    /**
     * {@inheritdoc}
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        throw new RuntimeException('Unsupported called method "validateCredentials"');
    }
}
