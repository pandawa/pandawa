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

use Lcobucci\JWT\Signer;
use RuntimeException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Signers
{
    const HS = 'hs';
    const RS = 'rs';

    /**
     * @var Signer[]
     */
    private $signers = [];

    public function __construct(array $signers)
    {
        foreach ($signers as $algo => $signer) {
            $this->add($signer);
        }
    }

    public function add(Signer $signer): void
    {
        $this->signers[$signer->getAlgorithmId()] = $signer;
    }

    public function get(string $algo): Signer
    {
        if (!array_key_exists($algo, $this->signers)) {
            throw new RuntimeException(sprintf('Signer with algo "%s" not found.', $algo));
        }

        return $this->signers[$algo];
    }
}
