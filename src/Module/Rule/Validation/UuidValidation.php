<?php
declare(strict_types=1);

namespace Pandawa\Module\Rule\Validation;

use Illuminate\Contracts\Validation\Rule;
use Ramsey\Uuid\Uuid;

/**
 * @author  Johan Tanaka <tanaka.johan@gmail.com>
 */
class UuidValidation implements Rule
{
    /**
     * {@inheritdoc}
     */
    public function passes($attribute, $value): bool
    {
        return Uuid::isValid($value);
    }

    /**
     * {@inheritdoc}
     */
    public function message(): string
    {
        return trans('pandawa-rule::validation.uuid');
    }
}