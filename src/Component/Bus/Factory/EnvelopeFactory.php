<?php

declare(strict_types=1);

namespace Pandawa\Component\Bus\Factory;

use Pandawa\Component\Bus\Stamp\MessageIdentifiedStamp;
use Pandawa\Component\Bus\Stamp\MessageNameStamp;
use Pandawa\Component\Bus\Stamp\SerializerStamp;
use Pandawa\Contracts\Bus\Envelope;
use Pandawa\Contracts\Bus\Message\RegistryInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class EnvelopeFactory
{
    public function __construct(protected readonly RegistryInterface $messageRegistry)
    {
    }

    public function wrap(object $message): Envelope
    {
        $envelope = Envelope::wrap($message);

        if ($envelope->last(MessageIdentifiedStamp::class)) {
            return $envelope;
        }

        $envelope = $envelope->with(new MessageIdentifiedStamp());

        if ($this->messageRegistry->has($messageClass = get_class($envelope->message))) {
            $metadata = $this->messageRegistry->get($messageClass);

            if (!$envelope->last(MessageNameStamp::class) && $name = $metadata->name) {
                $envelope = $envelope->with(new MessageNameStamp($name));
            }

            if (!$envelope->last(SerializerStamp::class) && $serializer = $metadata->serializer) {
                $envelope = $envelope->with(new SerializerStamp($serializer));
            }

            if (count($metadata->stamps)) {
                $envelope = $envelope->with(...$metadata->stamps);
            }
        }

        return $envelope;
    }
}
