<?php

declare(strict_types=1);

namespace Pandawa\Component\Bus\Serializer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class NativeSerializer implements SerializerInterface, NormalizerInterface, DenormalizerInterface
{
    public function serialize(mixed $data, string $format, array $context = []): string
    {
        return json_encode($this->normalize($data, $format, $context));
    }

    public function deserialize(mixed $data, string $type, string $format, array $context = []): mixed
    {
        return $this->denormalize(json_decode($data, true), $type, $format, $context);
    }

    public function normalize(mixed $object, string $format = null, array $context = [])
    {
        return [
            'type'       => 'native',
            'serialized' => serialize($object),
        ];
    }

    public function supportsNormalization(mixed $data, string $format = null)
    {
        return is_object($data);
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        return unserialize($data['serialized']);
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null)
    {
        return is_array($data) && 'native' === $data['type'];
    }
}
