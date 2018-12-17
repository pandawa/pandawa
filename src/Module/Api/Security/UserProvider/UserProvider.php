<?php
declare(strict_types=1);

namespace Pandawa\Module\Api\Security\UserProvider;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider as LaravelUserProvider;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Pandawa\Component\Ddd\Repository\EntityManagerInterface;
use Pandawa\Component\Ddd\Repository\Repository;
use Pandawa\Module\Api\Security\Contract\ActiveUserInterface;
use Pandawa\Module\Api\Security\Contract\UserProviderInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class UserProvider implements LaravelUserProvider
{
    /**
     * @var string
     */
    private $model;

    /**
     * @var Repository
     */
    private $repo;

    /**
     * @var HasherContract
     */
    private $hasher;

    /**
     * Constructor.
     *
     * @param string                 $model
     * @param EntityManagerInterface $entityManager
     * @param HasherContract         $hasher
     */
    public function __construct(string $model, EntityManagerInterface $entityManager, HasherContract $hasher)
    {
        $this->model = $model;
        $this->repo = $entityManager->getRepository($model);
        $this->hasher = $hasher;
    }

    /**
     * {@inheritdoc}
     */
    public function retrieveById($identifier)
    {
        $user = $this->repo->find($identifier);

        if (null !== $user && false === $this->validateUser($user)) {
            return null;
        }

        return $user;
    }

    /**
     * @param Authenticatable $user
     *
     * @return bool
     */
    private function validateUser(Authenticatable $user): bool
    {
        if ($user instanceof ActiveUserInterface) {
            return $user->isActive();
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function retrieveByToken($identifier, $token)
    {
        $user = $this->repo->findOneBy([$identifier => $token]);

        if (null !== $user && false === $this->validateUser($user)) {
            return null;
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     * @throws \ReflectionException
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user->setRememberToken($token);

        $this->repo->save($user);
    }

    /**
     * {@inheritdoc}
     */
    public function retrieveByCredentials(array $credentials)
    {
        if ($this->repo instanceof UserProviderInterface) {
            return $this->repo->findByCredentials($credentials);
        }

        unset($credentials['password']);

        return $this->repo->findOneBy($credentials);
    }

    /**
     * {@inheritdoc}
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        if (false === $this->validateUser($user)) {
            return false;
        }

        $plain = $credentials['password'] ?? null;

        return $this->hasher->check($plain, $user->getAuthPassword());
    }
}
