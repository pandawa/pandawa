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

namespace Pandawa\Module\Rule;

use Pandawa\Component\Module\AbstractModule;
use Pandawa\Component\Validation\RuleRegistryInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class PandawaRuleModule extends AbstractModule
{
    public function init(): void
    {
        $this->app->singleton(RuleRegistryInterface::class, config('modules.rule.registry'));
    }
}
