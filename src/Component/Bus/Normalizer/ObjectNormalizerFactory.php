<?php

declare(strict_types=1);

namespace Pandawa\Component\Bus\Normalizer;

use Pandawa\Contracts\Bus\Factory\NormalizerFactoryInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ObjectNormalizerFactory implements NormalizerFactoryInterface
{
    protected readonly NormalizerInterface $normalizer;

    public function __construct()
    {
        $this->normalizer = new ObjectNormalizer();
    }

    public function create(): NormalizerInterface
    {
        return $this->normalizer;
    }
}
