<?php
/**
 * This file is part of the Pandawa package.
 *
 * (c) 2018 Pandawa <https://github.com/bl4ckbon3/pandawa>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Pandawa\Module\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lcobucci\JWT\Signer\Hmac\Sha256 as Hs256;
use Lcobucci\JWT\Signer\Hmac\Sha512 as Hs512;
use Lcobucci\JWT\Signer\Rsa\Sha256 as Rsa256;
use Lcobucci\JWT\Signer\Rsa\Sha512 as Rsa512;
use Pandawa\Component\Module\AbstractModule;
use Pandawa\Module\Api\Routing\Loader\BasicLoader;
use Pandawa\Module\Api\Routing\Loader\GroupLoader;
use Pandawa\Module\Api\Routing\Loader\MessageLoader;
use Pandawa\Module\Api\Routing\RouteLoader;
use Pandawa\Module\Api\Routing\RouteLoaderInterface;
use Pandawa\Module\Api\Security\Authentication\AuthenticationManager;
use Pandawa\Module\Api\Security\Authentication\Authenticator\JwtAuthenticator;
use Pandawa\Module\Api\Security\Guard\AuthenticationGuard;
use Pandawa\Module\Api\Security\Jwt\Jwt;
use Pandawa\Module\Api\Security\Jwt\Keys;
use Pandawa\Module\Api\Security\Jwt\Signers;
use Pandawa\Module\Api\Security\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Pandawa\Module\Api\Security\TokenExtractor\ChainTokenExtractor;
use Pandawa\Module\Api\Security\TokenExtractor\QueryParameterTokenExtractor;
use Pandawa\Module\Api\Security\TokenExtractor\TokenExtractorInterface;
use Pandawa\Module\Api\Security\UserProvider\StatelessUserProvider;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class PandawaApiModule extends AbstractModule
{
    public function register(): void
    {
        $this->app->singleton(
            RouteLoaderInterface::class,
            function () {
                return new RouteLoader(
                    [
                        ['loader' => new GroupLoader(), 'priority' => 200],
                        ['loader' => new BasicLoader(), 'priority' => 100],
                        [
                            'loader'   => new MessageLoader((string) config('modules.api.controllers.invokable')),
                            'priority' => 0,
                        ],
                    ]
                );
            }
        );

        $this->registerSecurity();
    }

    public function boot(): void
    {
        parent::boot();

        Auth::provider(
            'stateless',
            function ($app, array $config) {
                return new StatelessUserProvider($config['model']);
            }
        );

        Auth::extend(
            'authenticator',
            function ($app, $name, array $config) {
                return new AuthenticationGuard(
                    Auth::createUserProvider($config['provider']),
                    $app[AuthenticationManager::class],
                    $app[Request::class],
                    (string) config('authenticator.default')
                );
            }
        );
    }

    private function registerSecurity(): void
    {
        $this->app->singleton(
            TokenExtractorInterface::class,
            function () {
                return new ChainTokenExtractor(
                    [
                        new AuthorizationHeaderTokenExtractor(),
                        new QueryParameterTokenExtractor('_token'),
                    ]
                );
            }
        );

        $this->app->singleton(
            Jwt::class,
            function () {
                $signers = new Signers(
                    [
                        'RS512' => new Rsa512(),
                        'RS256' => new Rsa256(),
                        'HS512' => new Hs512(),
                        'HS256' => new Hs256(),
                    ]
                );
                $keys = new Keys(config('modules.api.auth.jwt.keys'), $signers);

                return new Jwt($signers, $keys);
            }
        );

        $this->app->singleton(
            AuthenticationManager::class,
            function ($app) {
                $ttl = config('modules.api.auth.jwt.ttl');
                $algo = config('modules.api.auth.jwt.algo');

                return new AuthenticationManager(
                    [
                        new JwtAuthenticator($app[Jwt::class], (int) $ttl, (string) $algo),
                    ],
                    $this->app[TokenExtractorInterface::class]
                );
            }
        );
    }
}
