<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Bus\Factory;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface NormalizerFactoryInterface
{
    public function create(): NormalizerInterface;
}
