<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource\Formatter;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Pandawa\Contracts\Resource\Formatter\FormatterInterface;
use Pandawa\Contracts\Transformer\Context;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class YamlFormatter implements FormatterInterface
{
    public function getFormat(): string
    {
        return 'yaml';
    }

    public function getContentType(): string
    {
        return 'text/yaml';
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
        $acceptable = $request->getAcceptableContentTypes();

        return isset($acceptable[0]) && Str::contains(strtolower($acceptable[0]), ['/yaml', '/x-yaml']);
    }
}
