<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Event;

/**
 * @template TModel of Model
 *
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface HasDomainEventInterface
{
    /**
     * Add pending domain event.
     *
     * @param  object  $event
     *
     * @return TModel
     */
    public function recordThat(object $event): static;

    /**
     * Release all domain events.
     *
     * @return object[]
     */
    public function releaseDomainEvents(): array;
}
