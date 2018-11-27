<?php
declare(strict_types=1);

namespace Pandawa\Component\Module\Provider;

use Pandawa\Component\Presenter\PresenterInterface;
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

    protected function registerPresenterProvider(): void
    {
        if (file_exists($this->app->getCachedConfigPath())) {
            return;
        }

        $basePath = $this->getCurrentPath() . '/' . trim($this->presenterPath, '/');

        if (!is_dir($basePath)) {
            return;
        }

        foreach (Finder::create()->in($basePath)->files() as $presenter) {
            $presenterClass = $this->getClassFromFile($presenter);

            if (in_array(PresenterInterface::class, class_implements($presenterClass), true)
                && !(new ReflectionClass($presenterClass))->isAbstract()) {

                $this->mergeConfig('pandawa_presenters', [md5($presenterClass) => $presenterClass]);
            }
        }
    }
}
