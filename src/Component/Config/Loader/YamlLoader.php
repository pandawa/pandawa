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

        return Arr::map($config, function (mixed $item) use ($defines) {
            if (is_array($item)) {
                return $this->replaces($item, $defines);
            }

            if (is_string($item) && preg_match_all(self::DEFINE_REGEX, $item, $matches)) {
                foreach ($matches[1] as $i => $key) {
                    if (empty($replacer = $defines[$key] ?? null)) {
                        throw new \InvalidArgumentException(
                            sprintf('Variable "%s" is not defined.', $matches[0][$i])
                        );
                    }

                    $item = str_replace($matches[0][$i], $replacer, $item);
                }
            }

            return $item;
        });
    }
}
