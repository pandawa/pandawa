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

use Illuminate\Auth\AuthenticationException;
use Lcobucci\Jose\Parsing\Parser as Decoder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Token\Plain;
use Pandawa\Module\Api\Security\Contract\SignableUserInterface;
use Pandawa\Module\Api\Security\Jwt\Jwt;
use Pandawa\Module\Api\Security\Model\AuthenticatedUser;
use Pandawa\Module\Api\Security\Model\Signature;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class JwtAuthenticator implements AuthenticatorInterface
{
    const NAME = 'jwt';

    /**
     * @var Jwt
     */
    private $jwt;

    /**
     * @var string
     */
    private $defaultAlgo;

    /**
     * @var int
     */
    private $ttl;

    public function __construct(Jwt $jwt, int $ttl = null, string $defaultAlgo)
    {
        $this->jwt = $jwt;
        $this->defaultAlgo = $defaultAlgo;
        $this->ttl = $ttl;
    }

    public function sign(SignableUserInterface $user, array $payload = []): Signature
    {
        $claims = array_merge($user->getSignPayload(), $payload);

        if (!empty($this->ttl)) {
            $claims['exp'] = date('Y-m-d H:i:s', strtotime(sprintf('+%d seconds', $this->ttl)));
        }

        return new Signature((string) $this->jwt->sign($this->defaultAlgo, $claims), ['expires_in' => $this->ttl]);
    }

    /**
     * @param Signature $signature
     *
     * @return AuthenticatedUser
     * @throws AuthenticationException
     */
    public function verify(Signature $signature): ?AuthenticatedUser
    {
        $parser = new Parser(new Decoder());
        /** @var Plain $token */
        $token = $parser->parse($signature->getCredentials());

        if (true === $this->jwt->verify($token)) {
            return new AuthenticatedUser($token->claims()->get('sub'), $token->claims()->all());
        }

        return null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
