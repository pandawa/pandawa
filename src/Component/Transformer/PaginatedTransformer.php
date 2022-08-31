<?php

declare(strict_types=1);

namespace Pandawa\Component\Transformer;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Pandawa\Contracts\Transformer\Context;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class PaginatedTransformer extends CollectionTransformer
{
    public function process(Context $context, mixed $data): array
    {
        if ($data instanceof LengthAwarePaginator) {
            $data->setCollection(
                $data->getCollection()->map(
                    $this->map($context)
                )
            );

            return $data->toArray();
        }

        return parent::process($context, $data);
    }

    public function wrap(mixed $data): array
    {
        if (null === $this->getWrapper()) {
            return $data;
        }

        $paginationInformation = $this->paginationInformation($data);

        return [
            ...$paginationInformation,
            $this->getWrapper() => $data['data'],
        ];
    }

    protected function paginationInformation(array $data): array
    {
        return [
            'meta'  => $this->paginationMeta($data),
            'links' => $this->paginationLinks($data),
        ];
    }

    protected function paginationMeta(array $data): array
    {
        return Arr::except($data, [
            'data',
            'first_page_url',
            'last_page_url',
            'prev_page_url',
            'next_page_url',
            'links',
        ]);
    }

    protected function paginationLinks(array $data): array
    {
        return [
            'first' => $data['first_page_url'] ?? null,
            'last'  => $data['last_page_url'] ?? null,
            'prev'  => $data['prev_page_url'] ?? null,
            'next'  => $data['next_page_url'] ?? null,
        ];
    }

}
