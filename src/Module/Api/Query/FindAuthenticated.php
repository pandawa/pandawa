<?php
declare(strict_types=1);

namespace Pandawa\Module\Api\Query;

use Pandawa\Component\Message\AbstractQuery;
use Pandawa\Component\Message\NameableMessageInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class FindAuthenticated extends AbstractQuery implements NameableMessageInterface
{
    public static function name(): string
    {
        return 'pandawa:auth.me';
    }
}
