<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource\Middleware;

use Closure;
use Pandawa\Contracts\Resource\RendererMiddlewareInterface;
use Pandawa\Contracts\Resource\Rendering;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class AddClientIpMiddleware implements RendererMiddlewareInterface
{
    public function handle(Rendering $rendering, Closure $next): array
    {
        if ($ip = $rendering->context->request->ip()) {
            $rendering = $rendering->merge([
                'meta' => [
                    ...($rendering->data['meta'] ?? []),
                    'client_ip' => $ip,
                ],
            ]);
        }

        return $next($rendering);
    }
}
