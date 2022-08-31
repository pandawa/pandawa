<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Resource\Model;

use Illuminate\Contracts\Container\Container;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface FactoryInterface
{
    public function setContainer(Container $container): static;

    public function create(string $model): HandlerInterface;

    public function supports(string $model): bool;
}
