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
        foreach ($matches[0] as $i => $config) {
            $value = str_replace($config, self::normalize($callable($matches[1][$i])), $value);
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
