<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Bus\Factory;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface DenormalizerFactoryInterface
{
    public function create(): DenormalizerInterface;
}
