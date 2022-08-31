<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Resource;

use Pandawa\Contracts\Transformer\Context;
use Pandawa\Contracts\Transformer\TransformerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface RendererInterface
{
    public function setDefaultWrapper(?string $defaultWrapper): void;

    public function render(Context $context, mixed $result, TransformerInterface $resourceTransformer): Response;
}
