<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Annotation\Factory;

use Spiral\Attributes\ReaderInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface ReaderFactoryInterface
{
    public function create(): ReaderInterface;
}
