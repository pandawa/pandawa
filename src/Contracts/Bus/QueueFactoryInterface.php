<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Bus;

use Illuminate\Contracts\Queue\Queue;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface QueueFactoryInterface
{
    public function create(string $connection): Queue;

    public function supports(): bool;
}
