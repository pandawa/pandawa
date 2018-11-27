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

namespace Pandawa\Module\Resource;

use Pandawa\Component\Module\AbstractModule;
use Pandawa\Component\Resource\ResourceRegistryInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class PandawaResourceModule extends AbstractModule
{
    protected function init(): void
    {
        $this->app->singleton(ResourceRegistryInterface::class, function ($app) {
            $registryClass = config('modules.resource.registry');
            $resources = [];

            foreach (config('pandawa_resources') ?? [] as $resource) {
                $resources = array_merge($resources, $resource);
            }

            return new $registryClass($resources);
        });
    }
}
