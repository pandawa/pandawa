<?php
declare(strict_types=1);

namespace Pandawa\Module\Reactive;

use Exception;
use Pandawa\Component\Module\AbstractModule;
use Rx\Scheduler;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class PandawaReactiveModule extends AbstractModule
{
    /**
     * @throws Exception
     */
    protected function build(): void
    {
        Scheduler::setDefaultFactory(function () {
            return Scheduler::getImmediate();
        });
    }
}
