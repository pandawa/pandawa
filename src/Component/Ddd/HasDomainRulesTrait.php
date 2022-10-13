<?php

declare(strict_types=1);

namespace Pandawa\Component\Ddd;

use Pandawa\Contracts\Ddd\DomainRuleInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait HasDomainRulesTrait
{
    public function validate(DomainRuleInterface ...$domainRules): static
    {
        foreach ($domainRules as $rule) {
            $rule->validate();
        }

        return $this;
    }
}
