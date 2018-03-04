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

use Pandawa\Component\Ddd\Specification\SpecificationInterface;
use Pandawa\Component\Ddd\Specification\SpecificationRegistryInterface;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait SpecificationProviderTrait
{
    /**
     * @var string
     */
    protected $specificationPath = 'Specification';

    protected function bootSpecificationProvider(): void
    {
        $basePath = $this->getCurrentPath() . '/' . trim($this->specificationPath, '/');

        if (!is_dir($basePath) || null === $this->specificationRegistry()) {
            return;
        }

        foreach (Finder::create()->in($basePath)->files() as $specification) {
            $specificationClass = $this->getClassFromFile($specification);

            if (in_array(SpecificationInterface::class, class_implements($specificationClass), true)
                && !(new ReflectionClass($specificationClass))->isAbstract()) {

                $this->specificationRegistry()->add($specificationClass);
            }
        }
    }

    private function specificationRegistry(): ?SpecificationRegistryInterface
    {
        if (isset($this->app[SpecificationRegistryInterface::class])) {
            return $this->app[SpecificationRegistryInterface::class];
        }

        return null;
    }
}
