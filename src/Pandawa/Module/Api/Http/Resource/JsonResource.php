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

namespace Pandawa\Module\Api\Http\Resource;

use Illuminate\Http\Resources\CollectsResources;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;
use Pandawa\Component\Transformer\TransformerRegistryInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class JsonResource extends Resource
{
    use CollectsResources;

    /**
     * @var TransformerRegistryInterface
     */
    private $transformerRegistry;

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
     * @param                              $resource
     * @param TransformerRegistryInterface $transformer
     */
    public function __construct($resource, TransformerRegistryInterface $transformerRegistry)
    {
        parent::__construct($resource);

        if ($resource instanceof Collection || $resource instanceof AbstractPaginator) {
            $this->collectResource($resource);
        }

        $this->transformerRegistry = $transformerRegistry;
    }

    public function toArray($request)
    {
        if (null !== $this->collection) {
            return $this->collection->map(
                function ($data) use ($request) {
                    return $this->transformerRegistry->transform($request, $data);
                }
            );
        }

        return $this->transformerRegistry->transform($request, $this->resource);
    }

    public function toResponse($request)
    {
        return $this->resource instanceof AbstractPaginator
            ? (new PaginateResource($this))->toResponse($request)
            : parent::toResponse($request);
    }

    public function with($request)
    {
        $meta = [];
        $version = $this->version($request);
        $hostname = $this->hostname();

        if ($version) {
            $meta['version'] = $version;
        }

        if ($hostname) {
            $meta['hostname'] = $hostname;
        }

        if ($meta) {
            return ['meta' => $meta];
        }

        return [];
    }

    protected function version($request): string
    {
        $version = $request->route('version');

        if (!$version && config('modules.api.default_version')) {
            $version = config('modules.api.default_version');
        }

        return (string) $version;
    }

    protected function hostname(): ?string
    {
        if (true === config('modules.api.show_hostname')) {
            return gethostname();
        }

        return null;
    }
}
