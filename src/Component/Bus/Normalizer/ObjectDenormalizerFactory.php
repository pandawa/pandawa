<?php

declare(strict_types=1);

namespace Pandawa\Component\Bus\Normalizer;

use Pandawa\Contracts\Bus\Factory\DenormalizerFactoryInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ObjectDenormalizerFactory implements DenormalizerFactoryInterface
{
    protected readonly DenormalizerInterface $denormalizer;

    public function __construct()
    {
        $this->denormalizer = new ObjectNormalizer();
    }

    public function create(): DenormalizerInterface
    {
        return $this->denormalizer;
    }
}
