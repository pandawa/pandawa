<?php

declare(strict_types=1);

namespace Pandawa\Component\Validation;

use Illuminate\Contracts\Validation\Factory;
use Illuminate\Contracts\Validation\Validator;
use InvalidArgumentException;
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

    public function create(string|array $rule, array $data): Validator
    {
        if (is_array($rule)) {
            if (empty($rule['constraints'] ?? [])) {
                throw new InvalidArgumentException('Missing key "constraints" on rule.');
            }

            return $this->factory->make($data, $rule['constraints'] ?? [], $rule['messages'] ?? []);
        }

        $rule = $this->registry->get($rule);

        return $this->factory->make($data, $rule->constraints, $rule->messages);
    }
}
