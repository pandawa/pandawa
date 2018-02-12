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

namespace Pandawa\Module\Api\Http\Controller;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Pandawa\Module\Api\Transformer\AbstractTransformer;
use Pandawa\Module\Api\Transformer\CollectionTransformer;
use Pandawa\Module\Api\Transformer\Transformer;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait InteractsWithTransformerTrait
{
    protected function transform($results): AbstractTransformer
    {
        if ($results instanceof Collection || $results instanceof LengthAwarePaginator) {
            return new CollectionTransformer($results);
        }

        return new Transformer($results);
    }
}
