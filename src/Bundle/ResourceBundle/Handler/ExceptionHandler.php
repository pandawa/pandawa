<?php

declare(strict_types=1);

namespace Pandawa\Bundle\ResourceBundle\Handler;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Pandawa\Component\Foundation\Handler\ExceptionHandler as FoundationExceptionHandler;
use Pandawa\Component\Resource\Transformer\ErrorTransformer;
use Pandawa\Contracts\Resource\RendererInterface;
use Pandawa\Contracts\Transformer\Context;
use ReflectionClass;
use Throwable;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ExceptionHandler extends FoundationExceptionHandler
{
    protected array $errorCodeMaps = [
        InvalidArgumentException::class => 400,
        AuthenticationException::class => 401,
        AuthorizationException::class => 403,
        ModelNotFoundException::class => 404,
    ];

    public function render($request, Throwable $e)
    {
        $e = $this->prepareException($this->mapException($e));

        $context = new Context(
            options: [Context::HTTP_CODE => $this->getErrorCode($e), 'debug' => config('app.debug')],
            request: $request
        );

        return $this->renderer()->render($context, $e, new ErrorTransformer());
    }


    protected function renderer(): RendererInterface
    {
        return $this->container->get(RendererInterface::class);
    }

    protected function getErrorCode(Throwable $e): int
    {
        if ($this->isHttpException($e)) {
            return $e->getStatusCode();
        }

        if ($e->getCode() && is_int($e->getCode()) && $e->getCode() >= 400 && $e->getCode() < 500) {
            return $e->getCode();
        }

        return $this->getMapErrorCodes($e);
    }

    protected function getMapErrorCodes(Throwable $e): int
    {
        foreach ($this->errorCodeMaps as $errorClass => $errorCode) {
            if (is_a($e, $errorClass)) {
                return $errorCode;
            }
        }

        return 500;
    }

    protected function prepareException(Throwable $e): Throwable
    {
        return match (true) {
            $e instanceof RelationNotFoundException && $e->model => new RelationNotFoundException(
                sprintf(
                    'Relation "%s" was not found on resource "%s".',
                    Str::snake($e->relation),
                    Str::snake((new ReflectionClass($e->model))->getShortName())
                ),
                $e->getCode(),
                $e
            ),
            $e instanceof ModelNotFoundException && $e->getModel() => new ModelNotFoundException(
                sprintf(
                    'Resource "%s" with ids "%s" was not found.',
                    Str::snake((new ReflectionClass($e->getModel()))->getShortName()),
                    implode(',', $e->getIds() ?? [])
                )
            ),
            default => $e,
        };
    }
}
