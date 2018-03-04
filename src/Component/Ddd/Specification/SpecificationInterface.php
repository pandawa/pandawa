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

namespace Pandawa\Component\Ddd\Specification;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface SpecificationInterface
{
    /**
     * @param QueryBuilder|Builder $query
     */
    public function match($query): void;
}
