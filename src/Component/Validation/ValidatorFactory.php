<?php

declare(strict_types=1);

namespace Pandawa\Component\Validation;

use Illuminate\Contracts\Validation\Factory;
use Illuminate\Contracts\Validation\Validator;
use Pandawa\Contracts\Validation\FactoryInterface;
use Pandawa\Contracts\Validation\RuleRegistryInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ValidatorFactory implements FactoryInterface
{
    public function __construct(
        protected readonly RuleRegistryInterface $registry,
        protected readonly Factory $factory,
    ) {
    }

    public function create(string $rule, array $data): Validator
    {
        $rule = $this->registry->get($rule);

        return $this->factory->make($data, $rule->constraints, $rule->messages);
    }
}
