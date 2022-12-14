<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource\Http\Controller;

use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Pandawa\Component\Resource\Helper\ContextualTrait;
use Pandawa\Component\Resource\Helper\CriteriaTrait;
use Pandawa\Component\Resource\Helper\FilterTrait;
use Pandawa\Component\Resource\Helper\RepositoryTrait;
use Pandawa\Component\Resource\Helper\RequestValidationTrait;
use Pandawa\Component\Resource\Helper\RouteResourceTrait;
use Pandawa\Component\Resource\Helper\TransformerTrait;
use Pandawa\Component\Transformer\Transformer;
use Pandawa\Contracts\Resource\Model\FactoryResolverInterface;
use Pandawa\Contracts\Resource\Model\HandlerInterface;
use Pandawa\Contracts\Resource\RendererInterface;
use Pandawa\Contracts\Transformer\Context;
use Pandawa\Contracts\Transformer\TransformerInterface;
use Pandawa\Contracts\Validation\FactoryInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ResourceController
{
    use RouteResourceTrait,
        CriteriaTrait,
        RepositoryTrait,
        FilterTrait,
        ContextualTrait,
        TransformerTrait,
        RequestValidationTrait;

    public static int $defaultPagination = 10;

    public function __construct(
        protected readonly FactoryResolverInterface $factoryResolver,
        protected readonly RendererInterface $renderer,
        protected readonly FactoryInterface $validationFactory,
        protected readonly Container $container,
    ) {
    }

    public function index(Request $request): Response
    {
        return $this->view($request, function (HandlerInterface $handler) use ($request) {
            $pagination = $this->getRouteOption('paginate', $request, false);
            $params = [];

            if ($pagination) {
                $params['paginate'] = $request->query('limit', is_int($pagination) ? $pagination : static::$defaultPagination);
            }

            return $handler->find($params);
        });
    }

    public function show(Request $request): Response
    {
        return $this->view($request, function (HandlerInterface $handler) use ($request) {
            return $handler->findById($this->getResourceKey($request, $handler));
        });
    }

    public function store(Request $request): Response
    {
        return $this->persist($request, function (HandlerInterface $handler, array $data) {
            return $handler->store($data);
        });
    }

    public function update(Request $request): Response
    {
        return $this->persist($request, function (HandlerInterface $handler, array $data) use ($request) {
            return $handler->update($this->getResourceKey($request, $handler), $data);
        });
    }

    public function delete(Request $request): Response
    {
        $this->validateRequest($request);

        $resource = $this->getResource($request);
        $handler = $this->factoryResolver->resolve($resource)->create($resource);
        $id = $this->getResourceKey($request, $handler);

        $context = $this->createContext($request, $this->getRouteOptions($request));
        $transformer = $this->createTransformer($request, $handler->getDefaultTransformer());

        $this->applyRelation($handler, $context, $transformer);

        $result = $handler->findById($id);

        $handler->delete($id);

        return $this->renderer->render($context, $result, $transformer);
    }

    protected function getResourceKey(Request $request, HandlerInterface $handler): string|int
    {
        $key = $request->route(
            $this->getRouteOption('resource_key', $request, $handler->getModelKey())
        );

        if ($key instanceof Model) {
            return $key->getKey();
        }

        return $key;
    }

    protected function persist(Request $request, callable $callback): Response
    {
        $resource = $this->getResource($request);
        $handler = $this->factoryResolver->resolve($resource)->create($resource);
        $data = $this->validateRequest($request);

        $context = $this->createContext($request, $this->getRouteOptions($request));
        $transformer = $this->createTransformer($request, $handler->getDefaultTransformer());

        $result = $this->loadRelation($handler, $context, $transformer, $callback($handler, $data));

        return $this->renderer->render($context, $result, $transformer);
    }

    protected function view(Request $request, callable $fallbackHandler): Response
    {
        $this->validateRequest($request);

        $resource = $this->getResource($request);
        $handler = $this->factoryResolver->resolve($resource)->create($resource);

        $this->applyCondition($request, $handler);

        $context = $this->createContext($request, $this->getRouteOptions($request));

        $transformer = $this->createTransformer($request, $handler->getDefaultTransformer());

        $this->applyRelation($handler, $context, $transformer);

        $result = $this->callRepositoryIfPossible($request, $handler, $fallbackHandler);

        return $this->renderer->render($context, $result, $transformer);
    }

    protected function applyRelation(HandlerInterface $handler, Context $context, TransformerInterface $transformer): void
    {
        if ($transformer instanceof Transformer) {
            if (!empty($context->includes)) {
                $handler->withEager($transformer->getIncludes($context->includes));
            }
        }
    }

    protected function loadRelation(HandlerInterface $handler, Context $context, TransformerInterface $transformer, mixed $resource): mixed
    {
        if ($transformer instanceof Transformer) {
            if (!empty($context->includes)) {
                $handler->loadRelations($resource, $transformer->getIncludes($context->includes));
            }
        }

        return $resource;
    }

    protected function callRepositoryIfPossible(Request $request, HandlerInterface $handler, callable $fallback): mixed {
        if ($repository = $this->getRepository($request)) {
            return $this->callRepository($repository, $handler, $request);
        }

        return $fallback($handler);
    }

    protected function getRepository(Request $request): ?array
    {
        return $this->getRouteOption('repository', $request);
    }

    protected function applyCondition(Request $request, HandlerInterface $handler): void
    {
        $this
            ->applyFilter($handler, $request)
            ->applyCriteria($handler, $request)
        ;
    }
}
