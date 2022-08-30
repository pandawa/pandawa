<?php

declare(strict_types=1);

namespace Pandawa\Component\Validation;

use InvalidArgumentException;
use Pandawa\Contracts\Validation\Rule;
use Pandawa\Contracts\Validation\RuleRegistryInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class RuleRegistry implements RuleRegistryInterface
{
    /**
     * @var array<string, Rule>
     */
    protected array $rules = [];

    public function __construct(array $rules = [])
    {
        $this->load($rules);
    }

    public function load(array $rules): void
    {
        foreach ($rules as $name => $rule) {
            if (empty($rule['constraints'] ?? null)) {
                throw new InvalidArgumentException(sprintf('Rule "%s" should has constraints.', $name));
            }

            $this->add(new Rule($name, $rule['constraints'], $rule['messages'] ?? []));
        }
    }

    public function add(Rule $rule): void
    {
        $this->rules[$rule->name] = $rule;
    }

    public function all(): array
    {
        return $this->rules;
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->rules);
    }

    public function get(string $name): Rule
    {
        if ($this->has($name)) {
            return $this->rules[$name];
        }

        throw new InvalidArgumentException(sprintf('Rules "%s" is not registered.', $name));
    }
}
