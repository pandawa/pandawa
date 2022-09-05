<?php

declare(strict_types=1);

namespace Pandawa\Bundle\DependencyInjectionBundle\Plugin;

use Pandawa\Annotations\DependencyInjection\Injectable;
use Pandawa\Bundle\AnnotationBundle\Plugin\AnnotationServicePlugin;
use Pandawa\Bundle\DependencyInjectionBundle\Annotation\InjectableLoadHandler;
use Pandawa\Bundle\DependencyInjectionBundle\DependencyInjectionBundle;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ImportInjectableAnnotationPlugin extends AnnotationServicePlugin
{
    protected function getAnnotationClasses(): array
    {
        return [Injectable::class];
    }

    protected function getHandler(): string
    {
        return InjectableLoadHandler::class;
    }

    protected function getDirectories(): array
    {
        if (empty($this->directories)) {
            return [$this->bundle->getPath()];
        }

        return $this->directories;
    }

    protected function getConfigCacheKey(): string
    {
        return DependencyInjectionBundle::CONFIG_CACHE_KEY . '.injectable.' . $this->bundle->getName();
    }
}
