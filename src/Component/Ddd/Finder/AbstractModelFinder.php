<?php
declare(strict_types=1);

namespace Pandawa\Component\Ddd\Finder;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Pandawa\Component\Ddd\AbstractModel;
use Pandawa\Component\Ddd\Repository\EntityManagerInterface;
use Pandawa\Component\Ddd\Repository\Repository;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
abstract class AbstractModelFinder
{
    /**
     * @param string $id
     *
     * @return mixed|AbstractModel|null
     */
    public function findOrFail(string $id)
    {
        if (null !== $model = $this->repo()->find($id)) {
            return $model;
        }

        throw (new ModelNotFoundException())->setModel($this->getModelClass(), [$id]);
    }

    /**
     * @return Repository|mixed
     */
    protected function repo(): Repository
    {
        return $this->getEm()->getRepository($this->getModelClass());
    }

    /**
     * @return EntityManagerInterface
     */
    private function getEm(): EntityManagerInterface
    {
        return app()->get(EntityManagerInterface::class);
    }

    abstract protected function getModelClass(): string;
}
