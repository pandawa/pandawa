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

namespace Pandawa\Module\Rule\Validator;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Validation\Validator as LaravelValidator;
use Pandawa\Module\Rule\Validation\RuleValidation;
use Pandawa\Module\Rule\Validation\TreeRuleValidation;
use Pandawa\Module\Rule\Validation\UuidValidation;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class Validator extends LaravelValidator
{
    /**
     * {@inheritdoc}
     */
    public function validateUnique($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'unique');

        list($connection, $table) = $this->parseTable($parameters[0]);

        // The second parameter position holds the name of the column that needs to
        // be verified as unique. If this parameter isn't specified we will just
        // assume that this column to be verified shares the attribute's name.
        $column = $this->getQueryColumn($parameters, $attribute);

        list($idColumn, $id) = [null, null];

        if (isset($parameters[2])) {
            list($idColumn, $id) = $this->getUniqueIds($parameters);
        }

        // The presence verifier is responsible for counting rows within this store
        // mechanism which might be a relational database or any other permanent
        // data store like Redis, etc. We will use it to determine uniqueness.
        $verifier = $this->getPresenceVerifierFor($connection);

        $extra = $this->getUniqueExtra($parameters);

        if ($this->currentRule instanceof Unique) {
            $extra = array_merge($extra, $this->currentRule->queryCallbacks());
        }

        if (null !== $id) {
            if (preg_match('/^req\((\w+)\)$/', $id, $matches)) {
                $id = request(trim($matches[1]));
            } else if (preg_match('/^auth\((\w+)\)$/', $id, $matches)) {
                if (null !== $user = request()->user()) {
                    $id = $user->{trim($matches[1])};
                }
            }
        }

        return $verifier->getCount($table, $column, $value, $id, $idColumn, $extra) == 0;
    }

    /**
     * @param string $attribute
     * @param mixed  $value
     * @param array  $parameters
     *
     * @return bool
     */
    public function validateRule(string $attribute, $value, array $parameters): bool
    {
        /** @var RuleValidation $validation */
        $validation = $this->container->make(RuleValidation::class);
        if (!empty($value) && false === $passes = $validation->validate($attribute, $value, $parameters, $this)) {
            $this->failure($attribute, $validation, $parameters);
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param mixed  $value
     * @param array  $parameters
     *
     * @return bool
     */
    public function validateTreeRule(string $attribute, $value, array $parameters): bool
    {
        /** @var TreeRuleValidation $validation */
        $validation = $this->container->make(TreeRuleValidation::class);
        if (!empty($value) && false === $passes = $validation->validate($attribute, $value, $parameters, $this)) {
            $this->failure($attribute, $validation, $parameters);
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param mixed  $value
     * @param array  $parameters
     *
     * @return bool
     */
    public function validateUuid(string $attribute, $value, array $parameters): bool
    {
        /** @var UuidValidation $validation */
        $validation = $this->container->make(UuidValidation::class);
        if (false === $passes = $validation->passes($attribute, $value)) {
            $this->failure($attribute, $validation, $parameters);
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param mixed  $rule
     * @param array  $parameters
     */
    protected function failure(string $attribute, $rule, array $parameters): void
    {
        $this->failedRules[$attribute][get_class($rule)] = $parameters;
        if ($rule instanceof Rule) {
            $this->messages->add($attribute, $this->makeReplacements(
                $rule->message(), $attribute, get_class($rule), $parameters
            ));
        }
    }
}
