<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Enumerable;
use InvalidArgumentException;
use Pandawa\Component\Transformer\CollectionTransformer;
use Pandawa\Component\Transformer\PaginatedTransformer;
use Pandawa\Contracts\Resource\Formatter\FormatterInterface;
use Pandawa\Contracts\Resource\Formatter\FormatterResolverInterface;
use Pandawa\Contracts\Resource\RendererInterface;
use Pandawa\Contracts\Resource\Rendering;
use Pandawa\Contracts\Transformer\Context;
use Pandawa\Contracts\Transformer\TransformerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class Renderer implements RendererInterface
{
    protected Pipeline $pipeline;

    public function __construct(
        protected readonly Container $container,
        protected readonly FormatterResolverInterface $formatterResolver,
        protected readonly SerializerInterface $serializer,
        protected string $defaultContentType,
        protected ?string $defaultWrapper = null,
        protected array $middlewares = [],
        protected ?array $allowedFormats = [],
    ) {
        $this->pipeline = new Pipeline($this->container);
    }

    public function setDefaultWrapper(?string $defaultWrapper): void
    {
        $this->defaultWrapper = $defaultWrapper;
    }

    public function render(Context $context, mixed $result, TransformerInterface $resourceTransformer): Response {
        $transformer = $this->wrapTransformer($result, $resourceTransformer);

        if (null === $transformer->getWrapper()) {
            $transformer->setWrapper($this->defaultWrapper);
        }

        return $this->format($context, $this->toArray(
            $context,
            $transformer->wrap($transformer->process($context, $result))
        ));
    }

    public function format(Context $context, array $data): Response
    {
        $formatter = $this->getFormatter($context);

        return $formatter->toResponse(
            $context,
            $this->serialize(
                $formatter->getFormat(),
                $context,
                $data
            )
        );
    }

    public function toArray(Context $context, mixed $data): array
    {
        return $this->pipeline
            ->send(new Rendering($data, $context))
            ->through($this->middlewares)
            ->then(fn(Rendering $rendering) => $rendering->data);
    }

    protected function serialize(string $format, Context $context, array $data): string
    {
        if (!empty($this->allowedFormats) && !in_array($format, $this->allowedFormats)) {
            throw new InvalidArgumentException(sprintf('Format "%s" is not allowed.', $format));
        }

        return $this->serializer->serialize($data, $format, array_get($context->options, 'serialize.context', []));
    }

    protected function getFormatter(Context $context): FormatterInterface
    {
        $request = clone $context->request;

        if ($request->accepts(['text/html']) || !count($request->getAcceptableContentTypes())) {
            $request->headers->set(
                'Accept',
                $context->options['default_content_type'] ?? $this->defaultContentType
            );
        }

        if (null !== $formatter = $this->formatterResolver->resolve($request)) {
            return $formatter;
        }

        throw new InvalidArgumentException(
            sprintf('Unsupported format "%s".', implode(', ', $request->getAcceptableContentTypes()))
        );
    }

    protected function wrapTransformer(mixed $result, TransformerInterface $resourceTransformer): TransformerInterface
    {
        if ($this->isList($result)) {
            return $this->getCollectionTransformer($result, $resourceTransformer);
        }

        return $resourceTransformer;
    }

    protected function getCollectionTransformer(mixed $result, ?TransformerInterface $resourceTransformer): TransformerInterface
    {
        if ($result instanceof LengthAwarePaginator) {
            return new PaginatedTransformer($resourceTransformer);
        }

        return new CollectionTransformer($resourceTransformer);
    }

    protected function isList(mixed $result): bool
    {
        if ($result instanceof LengthAwarePaginator) {
            return true;
        }

        if ($result instanceof Enumerable) {
            return true;
        }

        if (is_array($result) && isset($result[0]) && is_array($result[0])) {
            return true;
        }

        return false;
    }
}
