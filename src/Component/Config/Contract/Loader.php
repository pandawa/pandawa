<?php

declare(strict_types=1);

namespace Pandawa\Component\Config\Contract;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface Loader
{
    public function load(string $file): array;

    public function supports(string $extension): bool;
}
