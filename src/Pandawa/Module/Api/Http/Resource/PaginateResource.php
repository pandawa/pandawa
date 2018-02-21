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

use Illuminate\Http\Resources\Json\PaginatedResourceResponse;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class PaginateResource extends PaginatedResourceResponse
{
    protected function paginationInformation($request): array
    {
        $paginated = $this->resource->resource->toArray();

        return [
            'links' => $this->paginationLinks($paginated),
            'meta'  => ['pagination' => $this->meta($paginated)],
        ];
    }
}
