<?php
declare(strict_types=1);

namespace Pandawa\Module\Api\Security\Contract;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface UserProviderInterface
{
    /**
     * @param array $credentials
     *
     * @return Authenticatable|mixed
     */
    public function findByCredentials(array $credentials);
}
