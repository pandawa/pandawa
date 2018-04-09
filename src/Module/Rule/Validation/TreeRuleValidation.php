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
final class TreeRuleValidation
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
     * @var bool
     */
    private $passes = true;

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
     * @param array     $parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validate(string $attribute, array $data, array $parameters, Validator $validator): bool
    {
        list($rules, $key) = $this->parseParameters($parameters);

        $func = function (array $tree, string $rule, string $key) use (&$func, $attribute, $validator) {
            foreach ($tree as $node) {
                if (false === $this->passes($node, $rule)) {
                    $this->fails($attribute, $validator);
                    $this->passes = false;
                }

                if ($child = $node[$key] ?? null) {
                    $func($child, $rule, $key);
                }
            }
        };

        foreach ($rules as $rule) {
            $func($data, $rule, $key);
        }

        return $this->passes;
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
     * @param array $parameters
     *
     * @return array
     */
    private function parseParameters(array $parameters): array
    {
        return [array_except($parameters, [0]), $parameters[0]];
    }
}