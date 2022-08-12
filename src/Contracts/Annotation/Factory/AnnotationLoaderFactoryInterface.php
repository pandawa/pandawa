<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Annotation\Factory;

use Pandawa\Contracts\Annotation\AnnotationLoaderInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface AnnotationLoaderFactoryInterface
{
    public function create(array $directories, array $exclude = [], array $scopes = []): AnnotationLoaderInterface;
}
