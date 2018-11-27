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

namespace Pandawa\Component\Ddd\Specification;

use Illuminate\Contracts\Foundation\Application;
use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class SpecificationRegistry implements SpecificationRegistryInterface
{
    /**
     * @var string[]
     */
    private $specifications;

    /**
     * @var Application
     */
    private $app;

    /**
     * Constructor.
     *
     * @param Application   $app
     * @param string[]|null $specs
     */
    public function __construct(Application $app, array $specs = null)
    {
        $this->app = $app;
        $this->specifications = $specs ?? [];
    }

    public function add(string $specificationClass): void
    {
        $implements = class_implements($specificationClass);

        if (!in_array(SpecificationInterface::class, $implements, true)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Specification class "%s" should implement "%s"',
                    $specificationClass,
                    SpecificationInterface::class
                )
            );
        }

        $name = $specificationClass;

        if (in_array(NameableSpecificationInterface::class, $implements, true)) {
            $name = $specificationClass::{'name'}();
        }

        $this->specifications[$name] = $specificationClass;
    }

    public function has(string $specification): bool
    {
        return array_key_exists($specification, $this->specifications);
    }

    /**
     * @param string $specification
     * @param array  $arguments
     *
     * @return SpecificationInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function get(string $specification, array $arguments = []): SpecificationInterface
    {
        if (!$this->has($specification)) {
            throw new RuntimeException(sprintf('Specification "%s" not found.', $specification));
        }

        $specificationClass = $this->specifications[$specification];

        return $this->app->make($specificationClass, $arguments);
    }
}
