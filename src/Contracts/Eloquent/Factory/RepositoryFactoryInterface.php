<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Eloquent\Factory;

use Pandawa\Contracts\Eloquent\RepositoryInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface RepositoryFactoryInterface
{
    public function create(string $modelClass, string $repositoryClass): RepositoryInterface;
}
