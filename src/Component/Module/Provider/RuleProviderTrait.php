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

use Pandawa\Component\Loader\ChainLoader;
use Pandawa\Component\Validation\RuleRegistryInterface;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait RuleProviderTrait
{
    /**
     * @var string
     */
    protected $rulePath = 'Resources/rules';

    protected function bootRuleProvider(): void
    {
        if (null === $this->ruleRegistry()) {
            return;
        }
    }

    protected function registerRuleProvider(): void
    {
        if (file_exists($this->app->getCachedConfigPath())) {
            return;
        }

        $basePath = $this->getCurrentPath() . '/' . trim($this->rulePath, '/');
        $loader = ChainLoader::create();

        if (is_dir($basePath)) {
            $finder = new Finder();

            /** @var SplFileInfo $file */
            foreach ($finder->in($basePath)->files() as $file) {
                $this->mergeConfig('pandawa_rules', $loader->load((string)$file));
            }
        }
    }

    private function ruleRegistry(): ?RuleRegistryInterface
    {
        if (isset($this->app[RuleRegistryInterface::class])) {
            return $this->app[RuleRegistryInterface::class];
        }

        return null;
    }
}
