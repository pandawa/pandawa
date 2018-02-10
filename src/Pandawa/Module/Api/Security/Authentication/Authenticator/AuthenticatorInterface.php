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

namespace Pandawa\Module\Api\Security\Authentication\Authenticator;

use Pandawa\Module\Api\Security\Contract\SignableUserInterface;
use Pandawa\Module\Api\Security\Model\AuthenticatedUser;
use Pandawa\Module\Api\Security\Model\Signature;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface AuthenticatorInterface
{
    public function sign(SignableUserInterface $user, array $payload = []): Signature;

    public function verify(Signature $signature): ?AuthenticatedUser;

    public function getName(): string;
}
