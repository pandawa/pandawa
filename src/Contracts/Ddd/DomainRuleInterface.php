<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Ddd;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface DomainRuleInterface
{
    public function validate(): void;
}
