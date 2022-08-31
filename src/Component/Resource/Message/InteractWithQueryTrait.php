<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource\Message;

use Pandawa\Contracts\Eloquent\RepositoryInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait InteractWithQueryTrait
{
    protected function applyQuery(Query $query, RepositoryInterface $repository): void
    {
        if (!empty($relations = $query->getRelations())) {
            $repository->withRelations($relations);
        }

        if (!empty($criteria = $query->getCriteria())) {
            $repository->withCriteria($criteria);
        }
    }
}
