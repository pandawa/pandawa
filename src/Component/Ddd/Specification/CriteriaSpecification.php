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

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class CriteriaSpecification implements SpecificationInterface
{
    /**
     * @var array
     */
    private $criteria;

    /**
     * Constructor.
     *
     * @param array $criteria
     */
    public function __construct(array $criteria)
    {
        $this->criteria = $criteria;
    }

    /**
     * @param QueryBuilder|Builder $query
     */
    public function match($query): void
    {
        foreach ($this->criteria as $key => $value) {
            $query->where($key, $value);
        }
    }
}
