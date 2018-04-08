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

namespace Pandawa\Module\Rule\Validation;

use Illuminate\Validation\Factory;
use Illuminate\Validation\Validator;
use Pandawa\Component\Validation\RuleRegistryInterface;

/**
 * @author  Johan Tanaka <tanaka.johan@gmail.com>
 */
final class RuleValidation
{
    /**
     * @var RuleRegistryInterface
     */
    private $registry;

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var array
     */
    private $validators;

    /**
     * @param RuleRegistryInterface $registry
     * @param Factory               $factory
     */
    public function __construct(RuleRegistryInterface $registry, Factory $factory)
    {
        $this->registry = $registry;
        $this->factory = $factory;
    }

    /**
     * @param string    $attribute
     * @param array     $data
     * @param array     $rules
     * @param Validator $validator
     *
     * @return bool
     */
    public function validate(string $attribute, array $data, array $rules, Validator $validator): bool
    {
        if (!$this->isMultiple($data)) {
            $data = [$data];
        }

        $passes = true;
        foreach ($rules as $rule) {
            foreach ($data as $datum) {
                if (false === $this->passes($datum, $rule)) {
                    $this->fails($attribute, $validator);
                    $passes = false;
                }
            }
        }

        return $passes;
    }

    /**
     * @param array  $data
     * @param string $rule
     *
     * @return bool
     */
    private function passes(array $data, string $rule): bool
    {
        $this->validators[$rule] = $this->factory->make($data, $this->constraints($rule), $this->messages($rule));

        return !$this->validators[$rule]->fails();
    }

    /**
     * @param string    $attribute
     * @param Validator $validator
     */
    private function fails(string $attribute, Validator $validator): void
    {
        /** @var Validator $internal */
        foreach ($this->validators as $internal) {
            $validator->getMessageBag()->merge([$attribute => $internal->getMessageBag()->messages()]);
        }
    }

    /**
     * @param string $rule
     *
     * @return array
     */
    private function constraints(string $rule): array
    {
        return array_get(array_get($this->registry->getAllRules(), $rule), 'constraints');
    }

    /**
     * @param string $rule
     *
     * @return array
     */
    private function messages(string $rule): array
    {
        return array_get(array_get($this->registry->getAllRules(), $rule), 'messages', []);
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    private function isMultiple(array $data): bool
    {
        return is_array($data[0] ?? null);
    }
}