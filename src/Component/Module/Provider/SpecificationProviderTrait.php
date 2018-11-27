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

use Pandawa\Component\Ddd\Specification\NameableSpecificationInterface;
use Pandawa\Component\Ddd\Specification\SpecificationInterface;
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
        if (file_exists($this->app->getCachedConfigPath())) {
            return;
        }

        $basePath = $this->getCurrentPath() . '/' . trim($this->specificationPath, '/');

        if (!is_dir($basePath)) {
            return;
        }

        foreach (Finder::create()->in($basePath)->files() as $specification) {
            $specificationClass = $this->getClassFromFile($specification);
            $implements = class_implements($specificationClass);

            if (in_array(SpecificationInterface::class, $implements, true)
                && !(new ReflectionClass($specificationClass))->isAbstract()) {

                $name = $specificationClass;
                if (in_array(NameableSpecificationInterface::class, $implements, true)) {
                    $name = $specificationClass::{'name'}();
                }

                $this->mergeConfig('pandawa_specs', [$name => $specificationClass]);
            }
        }
    }
}
