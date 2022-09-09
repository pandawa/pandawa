<?php

declare(strict_types=1);

namespace Pandawa\Bundle\AuthBundle\Plugin;

use Illuminate\Contracts\Auth\Access\Gate;
use Pandawa\Annotations\Auth\AsPolicy;
use Pandawa\Bundle\AnnotationBundle\Plugin\AnnotationPlugin;
use Pandawa\Bundle\AuthBundle\Annotation\PolicyLoadHandler;
use Pandawa\Bundle\AuthBundle\AuthBundle;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ImportPolicyAnnotationPlugin extends AnnotationPlugin
{
    protected ?string $defaultPath = 'Policy';

    public function boot(): void
    {
        $config = $this->bundle->getService('config');

        foreach ($config->get($this->getConfigKey(), []) as $model => $policy) {
            $this->gate()->policy($model, $policy);
        }
    }

    protected function getAnnotationClasses(): array
    {
        return [AsPolicy::class];
    }

    protected function getHandler(): string
    {
        return PolicyLoadHandler::class;
    }

    protected function gate(): Gate
    {
        return $this->bundle->getService(Gate::class);
    }

    protected function getConfigKey(): string
    {
        return AuthBundle::POLICY_CONFIG_KEY . '.' . $this->bundle->getName();
    }
}
