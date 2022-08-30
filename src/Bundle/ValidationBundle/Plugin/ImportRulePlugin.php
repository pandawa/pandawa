<?php

declare(strict_types=1);

namespace Pandawa\Bundle\ValidationBundle\Plugin;

use Pandawa\Bundle\ValidationBundle\ValidationBundle;
use Pandawa\Component\Foundation\Bundle\Plugin;
use Pandawa\Contracts\Config\LoaderInterface;
use Pandawa\Contracts\Validation\RuleRegistryInterface;
use Symfony\Component\Finder\Finder;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ImportRulePlugin extends Plugin
{
    public function __construct(protected readonly string $rulePath = 'Resources/rules')
    {
    }

    public function configure(): void
    {
        if ($this->bundle->getApp()->configurationIsCached()) {
            return;
        }

        $loadedRules = [];

        foreach ($this->getRules() as $rules) {
            $loadedRules = [...$loadedRules, ...$rules];
        }

        $config = $this->bundle->getService('config');
        $config->set(ValidationBundle::RULE_CONFIG_KEY, [
            ...$config->get(ValidationBundle::RULE_CONFIG_KEY, []),
            ...$loadedRules,
        ]);

        $this->bundle->callAfterResolving(
            'validation.rule_registry',
            function (RuleRegistryInterface $registry) use ($loadedRules) {
                $registry->load($loadedRules);
            }
        );
    }

    protected function getRules(): iterable
    {
        foreach (Finder::create()->in($this->bundle->getPath($this->rulePath))->files() as $file) {
            yield $this->loader()->load($file->getRealPath());
        }
    }

    protected function loader(): LoaderInterface
    {
        return $this->bundle->getService(LoaderInterface::class);
    }
}
