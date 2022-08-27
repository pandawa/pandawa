<?php

declare(strict_types=1);

namespace Pandawa\Component\Bus\Stamp;

use Pandawa\Contracts\Bus\StampInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class NormalizerStamp implements StampInterface
{
    public function __construct(public readonly string $normalizer)
    {
    }
}
