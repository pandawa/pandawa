<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Resource\Formatter;

use Illuminate\Http\Request;
use Pandawa\Contracts\Transformer\Context;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface FormatterInterface
{
    public function getFormat(): string;

    public function getContentType(): string;

    public function toResponse(Context $context, string $content): Response;

    public function supports(Request $request): bool;
}
