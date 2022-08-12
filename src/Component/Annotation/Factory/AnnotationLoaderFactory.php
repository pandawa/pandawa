<?php

declare(strict_types=1);

namespace Pandawa\Component\Annotation\Factory;

use Pandawa\Component\Annotation\AnnotationLoader;
use Pandawa\Contracts\Annotation\AnnotationLoaderInterface;
use Pandawa\Contracts\Annotation\Factory\AnnotationLoaderFactoryInterface;
use Pandawa\Contracts\Annotation\Factory\ReaderFactoryInterface;
use Spiral\Tokenizer\ClassesInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class AnnotationLoaderFactory implements AnnotationLoaderFactoryInterface
{
    public function __construct(protected readonly ReaderFactoryInterface $readerFactory)
    {
    }

    public function create(array $directories, array $exclude = [], array $scopes = []): AnnotationLoaderInterface
    {
        return new AnnotationLoader(
            $this->makeClassLocator($directories, $exclude, $scopes),
            $this->readerFactory->create()
        );
    }

    protected function makeClassLocator(array $directories, array $exclude, array $scopes): ClassesInterface
    {
        $tokenizer = new Tokenizer(
            new TokenizerConfig([
                'directories' => $directories,
                'exclude'     => $exclude,
                'scopes'      => $scopes,
            ])
        );

        return $tokenizer->classLocator();
    }
}
