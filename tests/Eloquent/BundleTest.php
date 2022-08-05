<?php

declare(strict_types=1);

namespace Test\Eloquent;

use Illuminate\Cache\CacheServiceProvider;
use Pandawa\Bundle\DatabaseBundle\DatabaseBundle;
use Pandawa\Bundle\DependencyInjectionBundle\DependencyInjectionBundle;
use Pandawa\Bundle\EloquentBundle\EloquentBundle;
use Pandawa\Bundle\FoundationBundle\FoundationBundle;
use Pandawa\Component\Eloquent\Repository;
use Pandawa\Component\Foundation\Application;
use Pandawa\Contracts\DependencyInjection\ServiceRegistryInterface;
use Pandawa\Contracts\Eloquent\Factory\CacheHandlerFactoryInterface;
use Pandawa\Contracts\Eloquent\Factory\QueryBuilderFactoryInterface;
use Pandawa\Contracts\Eloquent\Factory\RepositoryFactoryInterface;
use Pandawa\Contracts\Eloquent\Persistent\PersistentInterface;
use PHPUnit\Framework\TestCase;
use Test\Eloquent\Model\Post;
use Test\Eloquent\Model\User;
use Test\Eloquent\Repository\UserRepository;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class BundleTest extends TestCase
{
    /**
     * @dataProvider servicesProvider
     */
    public function testServicesAreRegistered(array $services): void
    {
        $app = $this->createApp();

        foreach ($services as $service) {
            $this->assertNotNull($app->get($service));
        }
    }

    public function testCreateRepository(): void
    {
        $app = $this->createApp();

        $registry = $app->get(ServiceRegistryInterface::class);
        $registry->load([
            'post_repo' => [
                'factory'   => ['@eloquent.factory.repository', 'create'],
                'arguments' => [
                    Post::class,
                ],
            ],
            'user_repo' => [
                'factory'   => ['@eloquent.factory.repository', 'create'],
                'arguments' => [
                    User::class,
                    UserRepository::class,
                ],
            ],
        ]);

        $this->assertNotNull($postRepo = $app->get('post_repo'));
        $this->assertSame(Repository::class, get_class($postRepo));

        $this->assertNotNull($userRepo = $app->get('user_repo'));
        $this->assertSame(UserRepository::class, get_class($userRepo));
    }

    public function servicesProvider(): array
    {
        return [
            'Test Factories' => [
                [
                    'eloquent.factory.query_builder',
                    'eloquent.factory.cache_handler',
                    'eloquent.factory.repository',
                    QueryBuilderFactoryInterface::class,
                    CacheHandlerFactoryInterface::class,
                    RepositoryFactoryInterface::class,
                ],
            ],
            'Test Services'  => [
                [
                    'eloquent.persistent',
                    PersistentInterface::class,
                ],
            ],
        ];
    }

    protected function createApp(): Application
    {
        $app = new Application();
        $app->register(new FoundationBundle($app));
        $app->register(new DependencyInjectionBundle($app));
        $app->register(new DatabaseBundle($app));
        $app->register(new EloquentBundle($app));
        $app->register(CacheServiceProvider::class);

        $app->configure();
        $app->boot();

        return $app;
    }
}
