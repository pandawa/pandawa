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

namespace Pandawa\Component\Message;

use Pandawa\Component\Ddd\Repository\Repository;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait InteractsWithRepositoryTrait
{
    final public function handle(AbstractQuery $query)
    {
        $repository = clone $this->repository();

        if ($query->isPaginated()) {
            $repository->paginate($query->getPageSize());
        }

        if ($query->hasRelations()) {
            $repository->with($query->getRelations());
        }

        if ($query->hasSpecifications()) {
            $repository->matches($query->getSpecifications());
        }

        return $this->run($repository, $query);
    }

    /**
     * @param Repository    $repository
     * @param AbstractQuery $query
     *
     * @return mixed
     */
    abstract protected function run($repository, $query);

    /**
     * @return Repository
     */
    abstract protected function repository();
}
