<?php

declare(strict_types=1);

namespace Pandawa\Bundle\ValidationBundle;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Validation\Factory;
use Illuminate\Validation\ValidationServiceProvider;
use Pandawa\Bundle\DependencyInjectionBundle\Plugin\ImportServicesPlugin;
use Pandawa\Bundle\FoundationBundle\Plugin\ImportConfigurationPlugin;
use Pandawa\Bundle\FoundationBundle\Plugin\RegisterBundlesPlugin;
use Pandawa\Component\Foundation\Application;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Component\Validation\Validator;
use Pandawa\Contracts\Foundation\HasPluginInterface;
use Pandawa\Contracts\Validation\FactoryInterface;
use Pandawa\Contracts\Validation\RuleRegistryInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ValidationBundle extends Bundle implements HasPluginInterface, DeferrableProvider
{
    const RULE_CONFIG_KEY = 'validation.rules';

    protected array $deferred = [
        'validator',
        'validation.parser.resolver',
        'validation.rule_registry',
        'validation.factory',
        RuleRegistryInterface::class,
        FactoryInterface::class,
    ];

    public function configure(): void
    {
        $this->app->singleton('validator', function (Application $app) {
            $validator = new Factory($app['translator'], $app);

            // The validation presence verifier is responsible for determining the existence of
            // values in a given data collection which is typically a relational database or
            // other persistent data stores. It is used to check for "uniqueness" as well.
            if (isset($app['db'], $app['validation.presence'])) {
                $validator->setPresenceVerifier($app['validation.presence']);
            }

            $validator->resolver(fn($translator, $data, $rules, $messages, $customAttributes) => new Validator(
                $translator,
                $data,
                $rules,
                $messages,
                $customAttributes
            ));

            return $validator;
        });
    }

    public function plugins(): array
    {
        return [
            new RegisterBundlesPlugin([ValidationServiceProvider::class]),
            new ImportConfigurationPlugin(),
            new ImportServicesPlugin(),
        ];
    }
}
