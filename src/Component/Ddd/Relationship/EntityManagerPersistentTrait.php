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

namespace Pandawa\Component\Ddd\Relationship;

use Pandawa\Component\Ddd\AbstractModel;
use Pandawa\Component\Ddd\Repository\EntityManagerInterface;
use ReflectionException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait EntityManagerPersistentTrait
{
    /**
     * @param AbstractModel $model
     *
     * @throws ReflectionException
     */
    protected function persist(AbstractModel $model): void
    {
        $this->em()->getRepository(get_class($model))->save($model);
    }

    /**
     * @return EntityManagerInterface
     */
    protected function em(): EntityManagerInterface
    {
        return app(EntityManagerInterface::class);
    }
}
