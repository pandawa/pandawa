<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Foundation;

use Illuminate\Contracts\Foundation\Application;

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
     * Boot the bundle.
     */
    public function boot(): void;

    /**
     * Configure the bundle.
     */
    public function configure(): void;

    public function callBootingCallbacks(): void;

    public function callBootedCallbacks(): void;

    public function when(): array;

    public function getName(): string;

    public function getShortName(): string;

    public function getNamespace(): string;

    public function getPath(): string;

    public function isDeferred(): bool;
}
