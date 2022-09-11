<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Validation;

use Illuminate\Contracts\Validation\Validator;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface FactoryInterface
{
    public function create(string|array $rule, array $data): Validator;
}
