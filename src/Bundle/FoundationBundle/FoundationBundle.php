<?php

declare(strict_types=1);

namespace Pandawa\Bundle\FoundationBundle;

use Illuminate;
use Illuminate\Contracts\Foundation\MaintenanceMode as MaintenanceModeContract;
use Illuminate\Foundation\MaintenanceModeManager;
use Illuminate\Http\Request;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\URL;
use Illuminate\Testing\LoggedExceptionCollection;
use Pandawa\Bundle\FoundationBundle\Plugin\RegisterBundlesPlugin;
use Pandawa\Component\Foundation\Bundle\Bundle;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class FoundationBundle extends Bundle
{
    protected array $registerBundles = [
        ServiceProvider\ConsoleServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
    ];

    public function register(): void
    {
        $this->registerRequestSignatureValidation();
        $this->registerExceptionTracking();
        $this->registerMaintenanceModeManager();
    }

    protected function registerRequestSignatureValidation(): void
    {
        Request::macro('hasValidSignature', function ($absolute = true) {
            return URL::hasValidSignature($this, $absolute);
        });

        Request::macro('hasValidRelativeSignature', function () {
            return URL::hasValidSignature($this, $absolute = false);
        });

        Request::macro('hasValidSignatureWhileIgnoring', function ($ignoreQuery = [], $absolute = true) {
            return URL::hasValidSignature($this, $absolute, $ignoreQuery);
        });
    }

    protected function registerExceptionTracking(): void
    {
        if (!$this->app->runningUnitTests()) {
            return;
        }

        $this->app->instance(
            LoggedExceptionCollection::class,
            new LoggedExceptionCollection
        );

        $this->app->make('events')->listen(MessageLogged::class, function ($event) {
            if (isset($event->context['exception'])) {
                $this->app->make(LoggedExceptionCollection::class)
                    ->push($event->context['exception']);
            }
        });
    }

    protected function registerMaintenanceModeManager(): void
    {
        $this->app->singleton(MaintenanceModeManager::class);

        $this->app->bind(
            MaintenanceModeContract::class,
            fn() => $this->app->make(MaintenanceModeManager::class)->driver()
        );
    }

    public function plugins(): array
    {
        return [
            new RegisterBundlesPlugin($this->registerBundles),
        ];
    }
}
