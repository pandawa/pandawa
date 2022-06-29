<?php

declare(strict_types=1);

namespace Pandawa\Component\Foundation\Handler;

use Illuminate\Foundation\Exceptions\Handler as LaravelExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ExceptionHandler extends LaravelExceptionHandler
{
    protected function getHttpExceptionView(HttpExceptionInterface $e): ?string
    {
        return null;
    }

    protected function registerErrorViewPaths(): void
    {
    }
}
