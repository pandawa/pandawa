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

namespace Pandawa\Component\Validation;

use Illuminate\Contracts\Validation\Factory;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class RuleRegistry implements RuleRegistryInterface
{
    /**
     * @var Factory
     */
    private $validationFactory;

    /**
     * @var array
     */
    private $rules;

    /**
     * Constructor.
     *
     * @param Factory $validationFactory
     * @param array   $rules
     */
    public function __construct(Factory $validationFactory, array $rules = null)
    {
        $this->validationFactory = $validationFactory;
        $this->rules = $rules ?? [];
    }

    public function getAllRules(): array
    {
        return $this->rules;
    }

    public function load(array $rules): void
    {
        $this->rules = array_merge($this->rules, $rules);
    }

    public function has(string $rule): bool
    {
        return array_key_exists($rule, $this->rules);
    }

    public function validate(string $rule, array $data): array
    {
        if (!$this->has($rule)) {
            throw new RuntimeException(sprintf('Rule "%s" is not registered.', $rule));
        }

        $rules = $this->rules[$rule];
        $constraints = (array) array_get($rules, 'constraints');
        $messages = (array) array_get($rules, 'messages');

        if (empty($constraints)) {
            throw new RuntimeException(sprintf('Rule "%s" has no constraints.', $rule));
        }

        $this->validationFactory->make($data, $constraints, $messages)->validate();

        return $this->filterData($constraints, $data);
    }

    private function filterData(array $constraints, array $data): array
    {
        $data = collect($data);
        $keys = collect($constraints)->keys()->map(
            function (string $rule) {
                return Str::contains($rule, '.') ? explode('.', $rule)[0] : $rule;
            }
        );

        return $data->only($keys->unique()->toArray())->all();
    }
}
