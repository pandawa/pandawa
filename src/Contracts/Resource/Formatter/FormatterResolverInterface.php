<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Resource\Formatter;

use Illuminate\Http\Request;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface FormatterResolverInterface
{
    public function resolve(Request $request): ?FormatterInterface;
}
