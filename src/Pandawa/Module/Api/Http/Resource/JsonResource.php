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

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;
use Pandawa\Component\Transformer\TransformerInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class JsonResource extends Resource
{
    /**
     * @var TransformerInterface
     */
    private $transformer;

    /**
     * Constructor.
     *
     * @param                      $resource
     * @param TransformerInterface $transformer
     */
    public function __construct($resource, TransformerInterface $transformer)
    {
        parent::__construct($resource);
        $this->transformer = $transformer;
    }

    public function toArray($request): array
    {
        if ($this->resource instanceof Collection) {
            return $this->resource->map(
                function ($resource) use ($request) {
                    return $this->transformer->transform($request, $resource);
                }
            );
        }

        return $this->transformer->transform($request, $this->resource);
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
