<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Bus;

use Illuminate\Contracts\Bus\QueueingDispatcher;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface BusInterface extends QueueingDispatcher
{
    public function mergePipes(array $pipes): static;

    public function wrap(object $message): Envelope;
}
