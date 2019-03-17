<?php
/**
 * This file is part of the Pandawa package.
 *
 * (c) 2018 Pandawa <https://github.com/bl4ckbon3/pandawa>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Pandawa\Module\Bus;

use Illuminate\Contracts\Bus\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Queue\Factory as QueueFactoryContract;
use Pandawa\Component\Bus\Pipe\DatabaseTransactionPipe;
use Pandawa\Component\Bus\Pipe\EventPublisherPipe;
use Pandawa\Component\Message\MessageRegistryInterface;
use Pandawa\Component\Module\AbstractModule;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class PandawaBusModule extends AbstractModule
{
    protected function build(): void
    {
        $pipes = [];

        if (true === config('modules.bus.enable_event_publisher', false)) {
            $pipes[] = EventPublisherPipe::class;
        }

        if (true === config('modules.bus.enable_db_transaction', false)) {
            $pipes[] = DatabaseTransactionPipe::class;
        }

        if (!empty($pipes)) {
            $this->dispatcher()->pipeThrough($pipes);
        }
    }

    protected function init(): void
    {
        $this->app->singleton(MessageRegistryInterface::class, function () {
            $registerClass = config('modules.bus.registry_class');

            return new $registerClass(config('pandawa_messages') ?? []);
        });

        $this->app->singleton(DispatcherContract::class, function ($app) {
            $className = config('modules.bus.dispatcher_class');

            return new $className(
                $app,
                $app[MessageRegistryInterface::class],
                function ($connection = null) use ($app) {
                    return $app[QueueFactoryContract::class]->connection($connection);
                }
            );
        });
    }

    private function dispatcher(): DispatcherContract
    {
        return $this->app[DispatcherContract::class];
    }
}
