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

namespace Pandawa\Component\Message;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class QueueEnvelope
{
    use InteractsWithQueue, Queueable;

    /**
     * @var AbstractCommand
     */
    private $command;

    /**
     * Constructor.
     *
     * @param AbstractCommand $command
     * @param string|null     $channel
     */
    public function __construct(AbstractCommand $command, string $channel = null)
    {
        $this->command = $command;
        $this->onQueue($channel);
    }

    public static function create(AbstractCommand $command): QueueEnvelope
    {
        return new QueueEnvelope($command);
    }

    public function getCommand(): AbstractCommand
    {
        return $this->command;
    }
}
