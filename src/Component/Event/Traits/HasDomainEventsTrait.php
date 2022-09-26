<?php

declare(strict_types=1);

namespace Pandawa\Component\Event\Traits;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait HasDomainEventsTrait
{
    protected array $pendingDomainEvents = [];

    public function recordThat(object $event): static
    {
        return tap($this, function () use ($event) {
            $this->pendingDomainEvents[] = $event;
        });
    }

    public function releaseDomainEvents(): array
    {
        return tap($this->pendingDomainEvents, function () {
            $this->pendingDomainEvents = [];
        });
    }
}
