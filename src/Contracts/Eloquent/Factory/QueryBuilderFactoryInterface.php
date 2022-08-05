<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Eloquent\Factory;

use Pandawa\Contracts\Eloquent\QueryBuilderInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface QueryBuilderFactoryInterface
{
    public function create(string $modelClass): QueryBuilderInterface;
}
