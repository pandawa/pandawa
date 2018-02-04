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

namespace Pandawa\Module\Api\Transformer;

use Illuminate\Http\Resources\CollectsResources;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class CollectionTransformer extends AbstractTransformer
{
    use CollectsResources;

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects;

    /**
     * The mapped collection instance.
     *
     * @var Collection
     */
    public $collection;

    /**
     * Constructor.
     *
     * @param mixed $resource
     */
    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->resource = $this->collectResource($resource);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray($request): array
    {
        return $this->collection->map->toArray($request)->all();
    }

    /**
     * {@inheritdoc}
     */
    public function toResponse($request): Response
    {
        return $this->resource instanceof AbstractPaginator
            ? (new PaginateTransformer($this))->toResponse($request)
            : parent::toResponse($request);
    }
}
