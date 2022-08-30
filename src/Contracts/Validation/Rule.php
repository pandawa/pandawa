<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Validation;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Rule
{
    public function __construct(
        public readonly string $name,
        public readonly array $constraints,
        public readonly array $messages,
    ) {
    }
}
