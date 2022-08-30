<?php

declare(strict_types=1);

namespace Test\Validation;

use Pandawa\Bundle\DatabaseBundle\DatabaseBundle;
use Pandawa\Bundle\DependencyInjectionBundle\DependencyInjectionBundle;
use Pandawa\Bundle\FoundationBundle\FoundationBundle;
use Pandawa\Bundle\TranslationBundle\TranslationBundle;
use Pandawa\Bundle\ValidationBundle\Plugin\ImportRulePlugin;
use Pandawa\Bundle\ValidationBundle\ValidationBundle;
use Pandawa\Component\Foundation\Application;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Contracts\Foundation\HasPluginInterface;
use Pandawa\Contracts\Validation\FactoryInterface;
use Pandawa\Contracts\Validation\RuleRegistryInterface;
use PHPUnit\Framework\TestCase;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class BundleTest extends TestCase
{
    /**
     * @dataProvider providedServices
     */
    public function testServiceRegistered($services): void
    {
        $app = $this->createApp();

        foreach ($services as $service) {
            $this->assertNotNull($app->get($service));
        }
    }

    public function testRulesLoaded(): void
    {
        $app = $this->createApp();

        /** @var RuleRegistryInterface $registry */
        $registry = $app['validation.rule_registry'];

        $this->assertTrue($registry->has('post.store'));

        $rule = $registry->get('post.store');

        $this->assertSame('post.store', $rule->name);
        $this->assertSame(['title' => 'required'], $rule->constraints);
        $this->assertSame(['title.required' => "Title can't be empty"], $rule->messages);
    }

    public function testValidate(): void
    {
        $app = $this->createApp();

        /** @var FactoryInterface $factory */
        $factory = $app['validation.factory'];

        $validator = $factory->create('post.store', ['title' => null]);
        $this->assertNotNull($validator);
        $this->assertTrue($validator->fails());
        $this->assertSame(['title' => ['Required' => []]], $validator->failed());
        $this->assertSame(['title' => ["Title can't be empty"]], $validator->errors()->messages());

        $validator = $factory->create('post.store', ['title' => 'Hello world', 'content' => 'Hello']);
        $this->assertSame(['title' => 'Hello world'], $validator->validated());
    }

    public function providedServices(): array
    {
        return [
            'Laravel Services' => [
                [
                    'validator',
                    'validation.presence',
                ]
            ],
            'Pandawa Services' => [
                [
                    'validation.rule_registry',
                    'validation.factory',
                    'validation.parser.resolver',
                ]
            ],
        ];
    }

    protected function createApp(): Application
    {
        $app = new Application();
        $app->instance('path.lang', __DIR__ . '/Resources/lang');

        $app->register(new FoundationBundle($app));
        $app->register(new DependencyInjectionBundle($app));
        $app->register(new TranslationBundle($app));
        $app->register(new ValidationBundle($app));
        $app->register(new DatabaseBundle($app));

        $app->register(new class($app) extends Bundle implements HasPluginInterface {
            public function plugins(): array
            {
                return [
                    new ImportRulePlugin(),
                ];
            }
        });

        $app->configure();
        $app->boot();

        return $app;
    }
}
