<?php

declare(strict_types=1);

namespace Pandawa\Component\Foundation\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class TrimStrings extends Middleware
{
    /**
     * The names of the attributes that should not be trimmed.
     *
     * @var array
     */
    protected $except = [
        'password',
        'password_confirmation',
    ];
}
