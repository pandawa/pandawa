<?php

declare(strict_types=1);

namespace Pandawa\Bundle\SchedulingBundle\Contract;

use Illuminate\Console\Scheduling\Schedule;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface SchedulerInterface
{
    public function schedule(Schedule $schedule): void;
}
