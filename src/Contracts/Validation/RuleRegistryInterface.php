<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Validation;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface RuleRegistryInterface
{
    /**
     * Load array of rules.
     *
     * @param  array<string, array{constraints: array<string, string>, messages: array<string, string>}> $rules
     */
    public function load(array $rules): void;

    /**
     * Add rule to registry.
     */
    public function add(Rule $rule): void;

    /**
     * Returns all rules.
     *
     * @return Rule[]
     */
    public function all(): array;

    /**
     * Return true if rule with given name exist, false otherwise.
     */
    public function has(string $name): bool;

    /**
     * Return rule with given name.
     */
    public function get(string $name): Rule;
}
