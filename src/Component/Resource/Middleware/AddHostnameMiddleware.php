<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource\Middleware;

use Closure;
use Pandawa\Contracts\Resource\RendererMiddlewareInterface;
use Pandawa\Contracts\Resource\Rendering;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class AddHostnameMiddleware implements RendererMiddlewareInterface
{
    public function handle(Rendering $rendering, Closure $next): array
    {
        if ($hostname = gethostname()) {
            $rendering = $rendering->merge([
                'meta' => [
                    ...($rendering->data['meta'] ?? []),
                    'hostname' => $hostname,
                ],
            ]);
        }

        return $next($rendering);
    }
}
