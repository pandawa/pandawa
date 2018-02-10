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

namespace Pandawa\Module\Api\Security\Authentication;

use Illuminate\Http\Request;
use Pandawa\Module\Api\Security\Authentication\Authenticator\AuthenticatorInterface;
use Pandawa\Module\Api\Security\Contract\SignableUserInterface;
use Pandawa\Module\Api\Security\Model\AuthenticatedUser;
use Pandawa\Module\Api\Security\Model\Signature;
use Pandawa\Module\Api\Security\TokenExtractor\TokenExtractorInterface;
use RuntimeException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class AuthenticationManager
{
    /**
     * @var AuthenticatorInterface[]
     */
    private $authenticators = [];

    /**
     * @var TokenExtractorInterface
     */
    private $tokenExtractor;

    /**
     * Constructor.
     *
     * @param AuthenticatorInterface[] $authenticators
     * @param TokenExtractorInterface  $tokenExtractor
     */
    public function __construct(array $authenticators, TokenExtractorInterface $tokenExtractor)
    {
        foreach ($authenticators as $authenticator) {
            $this->add($authenticator);
        }

        $this->tokenExtractor = $tokenExtractor;
    }

    /**
     * @param AuthenticatorInterface $authenticator
     */
    public function add(AuthenticatorInterface $authenticator)
    {
        $this->authenticators[$authenticator->getName()] = $authenticator;
    }

    public function sign(string $authenticator, SignableUserInterface $user, array $payload = []): Signature
    {
        $this->assertAuthenticatorExists($authenticator);

        return $this->authenticators[$authenticator]->sign($user, $payload);
    }

    public function verify(string $authenticator, Request $request): ?AuthenticatedUser
    {
        $this->assertAuthenticatorExists($authenticator);

        if (null !== $token = $this->tokenExtractor->extract($request)) {
            return $this->authenticators[$authenticator]->verify(new Signature($token, $request->all()));
        }

        return null;
    }

    public function has(string $authenticator): bool
    {
        return array_key_exists($authenticator, $this->authenticators);
    }

    private function assertAuthenticatorExists(string $authenticator): void
    {
        if (!$this->has($authenticator)) {
            throw new RuntimeException(sprintf('Authenticator with name "%s" not found.', $authenticator));
        }
    }
}
