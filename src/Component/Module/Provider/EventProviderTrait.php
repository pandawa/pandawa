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

namespace Pandawa\Component\Module\Provider;

use Illuminate\Contracts\Events\Dispatcher;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait EventProviderTrait
{
    abstract public function listens(): array;

    protected function bootEventProvider(): void
    {
        foreach ($this->listens() as $event => $listeners) {
            foreach ($listeners as $listener) {
                $this->eventListener()->listen($event, $listener);
            }
        }
    }

    protected function eventListener(): Dispatcher
    {
        return app(Dispatcher::class);
    }
}
