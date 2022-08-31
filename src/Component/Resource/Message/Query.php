<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource\Message;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
abstract class Query
{
    private array $criteria = [];
    private array $relations = [];
    private ?int $pagination = null;

    public function getCriteria(): array
    {
        return $this->criteria;
    }

    public function withCriteria(array $criteria): static
    {
        $this->criteria = $criteria;

        return $this;
    }

    public function getRelations(): array
    {
        return $this->relations;
    }

    public function withRelations(array $relations): static
    {
        $this->relations = $relations;

        return $this;
    }

    public function getPagination(): ?int
    {
        return $this->pagination;
    }

    public function paginate(int $limit): static
    {
        $this->pagination = $limit;

        return $this;
    }
}
