<?php

declare(strict_types=1);

namespace Pandawa\Component\Foundation;

use Illuminate\Support\ServiceProvider;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ResourcePublisher
{
    public static function publishes(string $class, array $paths, string|array $groups = null): void
    {
        static::ensurePublishArrayInitialized($class);

        ServiceProvider::$publishes[$class] = array_merge(ServiceProvider::$publishes[$class], $paths);

        foreach ((array)$groups as $group) {
            static::addPublishGroup($group, $paths);
        }
    }

    public static function ensurePublishArrayInitialized(string $class): void
    {
        if (!array_key_exists($class, ServiceProvider::$publishes)) {
            ServiceProvider::$publishes[$class] = [];
        }
    }

    public static function addPublishGroup(string $group, array $paths): void
    {
        if (!array_key_exists($group, ServiceProvider::$publishGroups)) {
            ServiceProvider::$publishGroups[$group] = [];
        }

        ServiceProvider::$publishGroups[$group] = array_merge(
            ServiceProvider::$publishGroups[$group], $paths
        );
    }
}
