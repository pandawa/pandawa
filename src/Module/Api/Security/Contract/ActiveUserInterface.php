<?php
declare(strict_types=1);

namespace Pandawa\Module\Api\Security\Contract;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface ActiveUserInterface
{
    public function isActive(): bool;
}
