<?php

declare(strict_types=1);

namespace Pandawa\Component\Config\Helpers;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class Replacer
{
    public static function replace(array $matches, string $value, callable $callable): mixed
    {
        foreach ($matches[0] as $i => $configKey) {
            $configValue = $callable($matches[1][$i]);

            if (is_array($configValue)) {
                return $configValue;
            }

            $value = str_replace($configKey, self::normalize($configValue), $value);
        }

        return self::cast($value);
    }

    public static function normalize(mixed $value): string
    {
        if (true === $value) {
            return 'true';
        }

        if (false === $value) {
            return 'false';
        }

        return (string)$value;
    }

    public static function cast(string $value): float|bool|int|string
    {
        if ('true' === $value) {
            return true;
        }

        if ('false' === $value) {
            return false;
        }

        if (preg_match('/^[0-9]+$/', $value)) {
            return (int)$value;
        }

        if (is_numeric($value)) {
            return (float)$value;
        }

        return $value;
    }
}
