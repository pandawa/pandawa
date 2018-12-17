<?php
declare(strict_types=1);

namespace Pandawa\Module\Api\Command;

use Pandawa\Component\Message\AbstractCommand;
use Pandawa\Component\Message\NameableMessageInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Authenticate extends AbstractCommand implements NameableMessageInterface
{
    public static function name(): string
    {
        return 'pandawa:auth';
    }
}
