<?php

declare(strict_types=1);

namespace Pandawa\Bundle\ResourceBundle\Plugin;

use Pandawa\Annotations\Resource\ApiMessage;
use Pandawa\Annotations\Resource\ApiResource;
use Pandawa\Bundle\AnnotationBundle\Plugin\AnnotationPlugin;
use Pandawa\Bundle\ResourceBundle\Annotation\ApiResourceLoadHandler;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ImportResourceAnnotationPlugin extends AnnotationPlugin
{
    protected function getAnnotationClasses(): array
    {
        return [ApiResource::class, ApiMessage::class];
    }

    protected function getHandler(): string
    {
        return ApiResourceLoadHandler::class;
    }

    protected function getDirectories(): array
    {
        if (empty($this->directories)) {
            return [$this->bundle->getPath()];
        }

        return $this->directories;
    }
}
