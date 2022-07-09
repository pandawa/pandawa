<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Config;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface LoaderInterface
{
    public function load(string $file): array;

    public function supports(string $extension): bool;
}
