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

namespace Pandawa\Module\Api;

use Pandawa\Component\Module\AbstractModule;
use Pandawa\Module\Api\Routing\Loader\BasicLoader;
use Pandawa\Module\Api\Routing\Loader\GroupLoader;
use Pandawa\Module\Api\Routing\Loader\MessageLoader;
use Pandawa\Module\Api\Routing\RouteLoader;
use Pandawa\Module\Api\Routing\RouteLoaderInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class PandawaApiModule extends AbstractModule
{
    public function register(): void
    {
        $this->app->singleton(
            RouteLoaderInterface::class,
            function () {
                return new RouteLoader(
                    [
                        ['loader' => new GroupLoader(), 'priority' => 200],
                        ['loader' => new BasicLoader(), 'priority' => 100],
                        [
                            'loader'   => new MessageLoader((string) config('modules.api.controllers.invokable')),
                            'priority' => 0,
                        ],
                    ]
                );
            }
        );
    }
}
