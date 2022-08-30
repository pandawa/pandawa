<?php

use Pandawa\Component\Validation;

return [
    /*
    |--------------------------------------------------------------------------
    | Rule Registry
    |--------------------------------------------------------------------------
    |
    | This rule registry class is used to store rules.
    |
    */
    'rule_registry' => Validation\RuleRegistry::class,

    /*
    |--------------------------------------------------------------------------
    | Validator Factory
    |--------------------------------------------------------------------------
    |
    | This factory class is used to create validator from a rule.
    |
    */
    'validator_factory' => Validation\ValidatorFactory::class,

    /*
    |--------------------------------------------------------------------------
    | Rules
    |--------------------------------------------------------------------------
    |
    | Here you may register your rules.
    |
    */
    'rules' => [],
];
