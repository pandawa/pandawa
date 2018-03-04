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

namespace Pandawa\Component\Ddd\Repository;

use Illuminate\Contracts\Foundation\Application;
use InvalidArgumentException;
use Pandawa\Component\Ddd\AbstractModel;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class EntityManager implements EntityManagerInterface
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var string
     */
    private $defaultRepositoryClass;

    /**
     * Constructor.
     *
     * @param Application $app
     * @param string      $defaultRepositoryClass
     */
    public function __construct(Application $app, string $defaultRepositoryClass)
    {
        $this->app = $app;
        $this->defaultRepositoryClass = $defaultRepositoryClass;
    }

    /**
     * @param string $modelClass
     *
     * @return RepositoryInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getRepository(string $modelClass): RepositoryInterface
    {
        if (!is_subclass_of($modelClass, AbstractModel::class)) {
            throw new InvalidArgumentException(
                sprintf('Model "%s" should sub class of "%s"', $modelClass, AbstractModel::class)
            );
        }

        $repositoryClass = $modelClass::{'getRepositoryClass'}() ?: $this->defaultRepositoryClass;

        if (!class_exists($repositoryClass)) {
            return new $this->defaultRepositoryClass($modelClass);
        }

        return new $repositoryClass($modelClass);
    }
}
