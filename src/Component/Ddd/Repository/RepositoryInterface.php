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

use Pandawa\Component\Ddd\AbstractModel;
use Pandawa\Component\Ddd\Specification\SpecificationInterface;
use Pandawa\Component\Ddd\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use ReflectionException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface RepositoryInterface
{
    /**
     * @return string
     */
    public function getModelClass(): string;

    /**
     * @param array $withs
     */
    public function with(array $withs): void;

    /**
     * @param int $pageSize
     */
    public function paginate(int $pageSize): void;

    /**
     * Match multiple specification.
     *
     * @param array $specifications
     */
    public function matches(array $specifications): void;

    /**
     * Match with specification.
     *
     * @param SpecificationInterface $specification
     */
    public function match(SpecificationInterface $specification): void;

    /**
     * @param mixed    $id
     * @param int|null $lockMode
     *
     * @return AbstractModel|mixed|null
     */
    public function find($id, int $lockMode = null): ?AbstractModel;

    /**
     * @param array $criteria
     *
     * @return AbstractModel|mixed
     */
    public function findOneBy(array $criteria): ?AbstractModel;

    /**
     * @param array $criteria
     *
     * @return Collection|LengthAwarePaginator|AbstractModel[]|mixed
     */
    public function findBy(array $criteria);

    /**
     * @return LengthAwarePaginator|mixed|AbstractModel[]|Collection
     */
    public function findAll();

    /**
     * Perform save model.
     *
     * @param AbstractModel|mixed $model
     *
     * @throws ReflectionException
     */
    public function save(AbstractModel $model): void;

    /**
     * Perform remove model.
     *
     * @param AbstractModel $model
     *
     * @throws ReflectionException
     */
    public function remove(AbstractModel $model): void;
}
