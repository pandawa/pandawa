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

use Pandawa\Component\Ddd\AbstractRepository;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait InteractsWithRepositoryTrait
{
    final public function handle(AbstractQuery $query)
    {
        if ($query->isPaginated()) {
            $this->repository()->paginate($query->getPageSize());
        }

        if ($query->hasRelations()) {
            $this->repository()->with($query->getRelations());
        }

        return $this->run($this->repository(), $query);
    }

    /**
     * @param AbstractRepository $repository
     * @param AbstractQuery      $query
     *
     * @return mixed
     */
    abstract protected function run($repository, $query);

    /**
     * @return AbstractRepository
     */
    abstract protected function repository();
}
