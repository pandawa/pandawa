<?php

declare(strict_types=1);

namespace Pandawa\Bundle\AuthBundle\Plugin;

use Pandawa\Annotations\Auth\UsePolicy;
use Pandawa\Bundle\AnnotationBundle\Plugin\AnnotationPlugin;
use Pandawa\Bundle\AuthBundle\Annotation\PolicyLoadHandler;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ImportPolicyAnnotationPlugin extends AnnotationPlugin
{
    protected ?string $defaultPath = 'Policy';

    protected function getAnnotationClasses(): array
    {
        return [UsePolicy::class];
    }

    protected function getHandler(): string
    {
        return PolicyLoadHandler::class;
    }
}
