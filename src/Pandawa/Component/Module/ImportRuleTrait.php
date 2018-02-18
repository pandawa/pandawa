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

namespace Pandawa\Component\Module;

use Pandawa\Component\Validation\RuleRegistryInterface;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait ImportRuleTrait
{
    protected function bootImportRule(): void
    {
        if (null === $this->registry()) {
            return;
        }

        $basePath = $this->getCurrentPath() . '/Resources/rule';

        if (is_dir($basePath)) {
            $finder = new Finder();

            /** @var SplFileInfo $file */
            foreach ($finder->in($basePath)->name('*.php') as $file) {
                $this->registry()->load(require (string) $file);
            }
        }
    }

    private function registry(): ?RuleRegistryInterface
    {
        if (isset($this->app[RuleRegistryInterface::class])) {
            return $this->app[RuleRegistryInterface::class];
        }

        return null;
    }
}
