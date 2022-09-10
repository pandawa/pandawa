<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource\Transformer;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Arr;
use Pandawa\Component\Transformer\Transformer;
use Pandawa\Contracts\Transformer\Context;
use ReflectionClass;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ErrorTransformer extends Transformer
{
    protected ?string $wrapper = 'error';

    protected function transform(Context $context, Throwable $exception): array
    {
        $reflection = new ReflectionClass(get_class($exception));

        if ($context->options['debug'] ?? false) {
            return [
                'message' => $exception->getMessage(),
                'code'    => $this->getErrorCode($context, $exception),
                'type'    => $reflection->getShortName(),
                'file'    => $exception->getFile(),
                'line'    => $exception->getLine(),
                'trace'   => collect($exception->getTrace())->map(
                    function ($trace) {
                        return Arr::except($trace, ['args']);
                    }
                )->all(),
            ];
        }

        return [
            'message' => $this->getErrorMessage($context, $exception),
            'code'    => $this->getErrorCode($context, $exception),
            'type'    => $reflection->getShortName(),
        ];
    }

    protected function getErrorCode(Context $context, Throwable $e): int
    {
        if (!empty($code = $e->getCode())) {
            return (int) $code;
        }

        return $context->options[Context::HTTP_CODE] ?? 0;
    }

    protected function getErrorMessage(Context $context, Throwable $e): string
    {
        if ($e instanceof HttpResponseException) {
            return $e->getMessage();
        }

        if ($this->isHttpException($e)) {
            return $e->getMessage();
        }

        $errorCode = $context->options[Context::HTTP_CODE] ?? 500;

        if ($errorCode >= 400 && $errorCode < 500) {
            return $e->getMessage();
        }

        return 'Unknown error.';
    }

    protected function isHttpException(Throwable $e): bool
    {
        return $e instanceof HttpExceptionInterface;
    }
}
