<?php

declare(strict_types=1);

namespace Pandawa\Component\Config\Loader;

use Illuminate\Support\Arr;
use Pandawa\Contracts\Config\LoaderInterface;
use Pandawa\Contracts\Config\Parser\ParserResolverInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class YamlLoader implements LoaderInterface
{
    const DEFINE_REGEX = '/\$\{([a-zA-Z0-9\.\-\_]+?)\}/';

    public function __construct(protected ParserResolverInterface $resolver)
    {
    }

    public function load(string $file): array
    {
        $config = Yaml::parseFile($file);

        if ($parser = $this->resolver->resolve($config)) {
            $config = $parser->parse($config);
            $config = $this->replaces(Arr::except($config, ['__defines']), $config['__defines'] ?? []);
        }

        return $config;
    }

    public function supports(string $extension): bool
    {
        return in_array($extension, ['yaml', 'yml'], true);
    }

    protected function replaces(array $config, array $defines): array
    {
        if (empty($defines)) {
            return $config;
        }

        $replaced = [];
        foreach ($config as $key => $value) {
            $key = $this->replaceValue($key, $defines);

            if (is_array($value)) {
                $replaced[$key] = $this->replaces($value, $defines);

                continue;
            }

            $replaced[$key] = $this->replaceValue($value, $defines);
        }

        return $replaced;
    }

    protected function replaceValue(mixed $value, array $defines): mixed
    {
        if (is_string($value) && preg_match_all(self::DEFINE_REGEX, $value, $matches)) {
            foreach ($matches[1] as $i => $key) {
                if (empty($replacer = $defines[$key] ?? null)) {
                    throw new \InvalidArgumentException(
                        sprintf('Variable "%s" is not defined.', $matches[0][$i])
                    );
                }

                $value = str_replace($matches[0][$i], $replacer, $value);
            }
        }

        return $value;
    }
}
