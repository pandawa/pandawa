<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource\Formatter;

use Illuminate\Http\Request;
use Pandawa\Contracts\Resource\Formatter\FormatterInterface;
use Pandawa\Contracts\Transformer\Context;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class JsonFormatter implements FormatterInterface
{
    public function getFormat(): string
    {
        return 'json';
    }

    public function getContentType(): string
    {
        return 'application/json';
    }

    public function toResponse(Context $context, string $content): Response
    {
        return new Response(
            $content,
                $context->options[Context::HTTP_CODE] ?? 200,
            ['Content-Type' => $this->getContentType()]
        );
    }

    public function supports(Request $request): bool
    {
        return $request->acceptsJson();
    }
}
