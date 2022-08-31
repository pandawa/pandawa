<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource\Middleware;

use Closure;
use Pandawa\Contracts\Resource\RendererMiddlewareInterface;
use Pandawa\Contracts\Resource\Rendering;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class AddVersionMiddleware implements RendererMiddlewareInterface
{
    public function handle(Rendering $rendering, Closure $next): array
    {
        if ($version = $rendering->context->version) {
            $rendering = $rendering->merge([
                'meta' => [
                    ...($rendering->data['meta'] ?? []),
                    'version' => $version,
                ],
            ]);
        }

        return $next($rendering);
    }
}
