<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource\Http\Controller;

use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Pandawa\Component\Resource\Helper\ContextualTrait;
use Pandawa\Component\Resource\Helper\CriteriaTrait;
use Pandawa\Component\Resource\Helper\RequestValidationTrait;
use Pandawa\Component\Resource\Helper\RouteResourceTrait;
use Pandawa\Component\Resource\Helper\TransformerTrait;
use Pandawa\Component\Resource\Message\Query;
use Pandawa\Component\Transformer\DataTransformer;
use Pandawa\Component\Transformer\Transformer;
use Pandawa\Contracts\Bus\BusInterface;
use Pandawa\Contracts\Bus\Message\RegistryInterface;
use Pandawa\Contracts\Resource\RendererInterface;
use Pandawa\Contracts\Transformer\Context;
use Pandawa\Contracts\Transformer\TransformerInterface;
use Pandawa\Contracts\Validation\FactoryInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class MessageController
{
    use RouteResourceTrait,
        ContextualTrait,
        TransformerTrait,
        RequestValidationTrait,
        CriteriaTrait;

    public function __construct(
        protected readonly BusInterface $bus,
        protected readonly RegistryInterface $messageRegistry,
        protected readonly RendererInterface $renderer,
        protected readonly FactoryInterface $validationFactory,
        protected readonly Container $container,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $context = $this->createContext($request, $this->getRouteOptions($request));
        $transformer = $this->createTransformer($request, new DataTransformer());

        $message = $this->setupIfQuery(
            $this->makeMessage($request),
            $context,
            $transformer
        );

        $result = $this->bus->dispatch($message);

        return $this->renderer->render($context, $result, $transformer);
    }

    protected function setupIfQuery(object $message, Context $context, TransformerInterface $transformer): object
    {
        if ($message instanceof Query) {
            $this
                ->setupQueryCriteria($message, $context->request)
                ->setupQueryPagination($message, $context->request)
            ;

            if ($transformer instanceof Transformer && !empty($context->includes)) {
                $message->withRelations(
                    $transformer->getIncludes(
                        $context->includes
                    )
                );
            }
        }

        return $message;
    }

    protected function setupQueryPagination(Query $query, Request $request): static
    {
        if ($pagination = $this->getRouteOption('paginate', $request, false)) {
            $query->paginate((int) $request->query('limit', is_int($pagination) ? $pagination : ResourceController::$defaultPagination));
        }

        return $this;
    }

    protected function setupQueryCriteria(Query $query, Request $request): static
    {
        $query->withCriteria(
            $this->getCriteria($request)
        );

        return $this;
    }

    protected function makeMessage(Request $request): object
    {
        $data = $this->validateRequest($request);
        $normalized = [];

        foreach ($data as $key => $value) {
            $normalized[Str::camel($key)] = $value;
        }

        return $this->container->make($this->getMessage($request), $normalized);
    }

    protected function getMessage(Request $request): string
    {
        $resource = $this->getRouteOption('message', $request);

        if ($this->messageRegistry->hasName($resource)) {
            return $this->messageRegistry->getByName($resource)->class;
        }

        return $resource;
    }
}
