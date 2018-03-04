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

namespace Pandawa\Module\Api\Security\Jwt;

use DateTimeImmutable;
use Illuminate\Auth\AuthenticationException;
use Lcobucci\Jose\Parsing\Parser;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Token\Plain;


/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Jwt
{
    /**
     * @var Signers
     */
    private $signers;

    /**
     * @var Keys
     */
    private $keys;

    public function __construct(Signers $signers, Keys $keys)
    {
        $this->signers = $signers;
        $this->keys = $keys;
    }

    public function sign(string $algo, array $claims): Plain
    {
        $tokenBuilder = new Builder(new Parser());

        foreach ($claims as $key => $value) {
            switch ($key) {
                case 'id':
                    $tokenBuilder->identifiedBy($value);
                    break;
                case 'sub':
                    $tokenBuilder->relatedTo($value);
                    break;
                case 'exp':
                    $tokenBuilder->expiresAt(new DateTimeImmutable($value));
                    break;
                default:
                    $tokenBuilder->withClaim($key, $value);
            }
        }

        return $tokenBuilder->getToken($this->signers->get($algo), $this->keys->getEncryptKey($algo));
    }

    /**
     * @param Plain $token
     *
     * @return bool
     * @throws AuthenticationException
     */
    public function verify(Plain $token): bool
    {
        /** @var string $algo */
        $algo = $token->headers()->get('alg');
        $verified = $this->signers->get($algo)->verify(
            $token->signature()->hash(),
            $token->payload(),
            $this->keys->getDecryptKey($algo)
        );

        if ($verified) {
            if ($token->isExpired(new DateTimeImmutable())) {
                throw new AuthenticationException('The given token has been expired.');
            }

            return true;
        }

        return false;
    }
}
