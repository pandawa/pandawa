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

use InvalidArgumentException;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Key\LocalFileReference;
use Lcobucci\JWT\Signer\Rsa;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Keys
{
    /**
     * @var array
     */
    private $keys = [];

    /**
     * @var Signers
     */
    private $signers;

    public function __construct(array $keys, Signers $signers)
    {
        $this->keys = $keys;
        $this->signers = $signers;
    }

    public function getEncryptKey(string $algo): Key
    {
        $key = $this->getKeyName($algo);
        $this->assertExistKey($key);

        if ($this->signers->get($algo) instanceof Rsa) {
            return LocalFileReference::file(
                array_get($this->keys[$key], 'private_key'),
                array_get($this->keys[$key], 'passphrase', '')
            );
        }

        return InMemory::plainText(array_get($this->keys[$key], 'secret_key'));
    }

    public function getDecryptKey(string $algo): Key
    {
        $key = $this->getKeyName($algo);
        $this->assertExistKey($key);

        if ($this->signers->get($algo) instanceof Rsa) {
            return LocalFileReference::file(array_get($this->keys[$key], 'public_key'));
        }

        return InMemory::plainText(array_get($this->keys[$key], 'secret_key'));
    }

    private function getKeyName(string $algo): string
    {
        return strtolower(substr($algo, 0, 2));
    }

    private function assertExistKey(string $key): void
    {
        if (!isset($this->keys[$key])) {
            throw new InvalidArgumentException(sprintf('Missing jwt key for "%s"', $key));
        }
    }
}
