<?php

declare(strict_types=1);

namespace Pandawa\Component\Bus\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Pandawa\Component\Bus\Stamp\DatabaseTransactionStamp;
use Pandawa\Contracts\Bus\Envelope;
use Pandawa\Contracts\Bus\MiddlewareInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class RunInDatabaseTransactionMiddleware implements MiddlewareInterface
{
    public function handle(Envelope $envelope, Closure $next): mixed
    {
        if ($envelope->last(DatabaseTransactionStamp::class)) {
            return DB::transaction(fn() => $next($envelope));
        }

        return $next($envelope);
    }
}
