<?php

declare(strict_types=1);

namespace Pandawa\Bundle\AnnotationBundle\Plugin;

use Pandawa\Component\Foundation\Bundle\Plugin;
use Pandawa\Contracts\Annotation\AnnotationLoaderInterface;
use Pandawa\Contracts\Annotation\AnnotationLoadHandlerInterface;
use Pandawa\Contracts\Annotation\Factory\AnnotationLoaderFactoryInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ImportAnnotationPlugin extends Plugin
{
    public function __construct(
        protected readonly array $annotationClasses,
        protected readonly array $directories,
        protected readonly string $classHandler,
        protected readonly ?string $targetClass = null,
        protected readonly bool $dontRunIfCached = true,
        protected readonly array $exclude = [],
        protected readonly array $scopes = [],
    ) {
    }

    public function configure(): void
    {
        if ($this->dontRunIfCached && $this->bundle->getApp()->configurationIsCached()) {
            return;
        }

        $handler = $this->bundle->getApp()->make($this->classHandler);

        if (!$handler instanceof AnnotationLoadHandlerInterface) {
            throw new \InvalidArgumentException(sprintf(
                'Class "%s" should implement "%s".',
                $this->classHandler,
                AnnotationLoadHandlerInterface::class
            ));
        }

        $handler->setBundle($this->bundle);

        foreach ($this->annotationClasses as $annotationClass) {
            foreach ($this->loader()->load($annotationClass, $this->targetClass) as $option) {
                $handler->handle($option);
            }
        }
    }

    protected function loader(): AnnotationLoaderInterface
    {
        return $this->loaderFactory()->create($this->directories, $this->exclude, $this->scopes);
    }

    protected function loaderFactory(): AnnotationLoaderFactoryInterface
    {
        return $this->bundle->getService(AnnotationLoaderFactoryInterface::class);
    }
}
