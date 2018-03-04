<?php
/**
 * This file is part of the Pandawa package.
 *
 * (c) 2018 Pandawa <https://github.com/bl4ckbon3/pandawa>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Pandawa\Component\Bus\Pipe;

use Pandawa\Component\Message\AbstractCommand;
use Pandawa\Component\Message\AbstractMessage;
use DB;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class DatabaseTransactionPipe
{
    /**
     * Handle command in database transaction.
     *
     * @param AbstractMessage|mixed $message
     * @param mixed                 $next
     *
     * @return mixed
     * @throws \Exception
     * @throws \Throwable
     */
    public function handle($message, $next)
    {
        if ($message instanceof AbstractCommand) {
            return DB::transaction(
                function () use ($message, $next) {
                    return $next($message);
                }
            );
        }

        return $next($message);
    }
}
