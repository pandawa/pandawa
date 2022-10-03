<?php

declare(strict_types=1);

namespace Pandawa\Bundle\ViewBundle\Plugin;

use Pandawa\Component\Foundation\Bundle\Plugin;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class RegisterViewPathPlugin extends Plugin
{
    public function __construct(protected readonly string $viewPath = 'Resources/views')
    {
    }

    public function boot(): void
    {
        $this->loadViewsFrom(
            $this->bundle->getPath($this->viewPath),
            $this->bundle->getName()
        );
    }

    protected function loadViewsFrom($path, $namespace)
    {
        $this->bundle->callAfterResolving('view', function ($view) use ($path, $namespace) {
            if (isset($this->app->config['view']['paths']) &&
                is_array($this->app->config['view']['paths'])) {
                foreach ($this->app->config['view']['paths'] as $viewPath) {
                    if (is_dir($appPath = $viewPath.'/vendor/'.$namespace)) {
                        $view->addNamespace($namespace, $appPath);
                    }
                }
            }

            $view->addNamespace($namespace, $path);
        });
    }
}
