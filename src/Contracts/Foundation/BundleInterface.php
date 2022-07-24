<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Foundation;

use Pandawa\Component\Foundation\Application;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface BundleInterface
{
    public function getApp(): Application;

    /**
     * Configure all plugins.
     */
    public function configurePlugin(): void;

    /**
     * Boot all plugins.
     */
    public function bootPlugin(): void;

    /**
     * Register the bundle.
     */
    public function register(): void;

    /**
     * Boot the bundle.
     */
    public function boot(): void;

    /**
     * Configure the bundle.
     */
    public function configure(): void;

    public function getService(string $name): mixed;

    public function callBootingCallbacks(): void;

    public function callBootedCallbacks(): void;

    public function when(): array;

    public function getName(): string;

    public function getNamespace(): string;

    public function getPath(?string $path = null): string;

    public function isDeferred(): bool;
}
