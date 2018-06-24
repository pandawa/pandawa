<?php
declare(strict_types=1);

namespace Pandawa\Component\Module\Provider;

use Pandawa\Component\Presenter\PresenterInterface;
use Pandawa\Component\Presenter\PresenterRegistryInterface;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait PresenterProviderTrait
{
    /**
     * @var string
     */
    protected $presenterPath = 'Presenter';

    protected function bootPresenterProvider(): void
    {
        $basePath = $this->getCurrentPath() . '/' . trim($this->presenterPath, '/');

        if (!is_dir($basePath) || null === $this->presenterRegistry()) {
            return;
        }

        foreach (Finder::create()->in($basePath)->files() as $presenter) {
            $presenterClass = $this->getClassFromFile($presenter);

            if (in_array(PresenterInterface::class, class_implements($presenterClass), true)
                && !(new ReflectionClass($presenterClass))->isAbstract()) {

                $this->presenterRegistry()->add($presenterClass);
            }
        }
    }

    private function presenterRegistry(): ?PresenterRegistryInterface
    {
        if (isset($this->app[PresenterRegistryInterface::class])) {
            return $this->app[PresenterRegistryInterface::class];
        }

        return null;
    }
}