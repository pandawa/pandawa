<?php
/**
 * This file is part of the Pandawa package.
 *
 * (c) 2018 Pandawa <https://github.com/bl4ckbon3/pandawa>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Pandawa\Module\Api\Http\Controller;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Route;
use Illuminate\Support\Str;
use Pandawa\Component\Ddd\AbstractModel;
use Pandawa\Component\Ddd\Repository\EntityManagerInterface;
use Pandawa\Component\Ddd\Repository\RepositoryInterface;
use Pandawa\Component\Ddd\Specification\SpecificationRegistryInterface;
use Pandawa\Component\Resource\ResourceRegistryInterface;
use Pandawa\Component\Validation\RequestValidationTrait;
use ReflectionException;
use RuntimeException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ResourceController extends Controller implements ResourceControllerInterface
{
    use InteractsWithRelationsTrait, InteractsWithRendererTrait, RequestValidationTrait;

    /**
     * @param Request $request
     *
     * @return Responsable
     */
    public function show(Request $request): Responsable
    {
        $route = $request->route();

        if (null !== $repository = array_get($route->defaults, 'repository.show')) {
            $result = $this->callRepository($request, $repository, 'show');
        } else {
            $modelClass = $this->getModelClass($route);
            $key = $this->getModelKey($modelClass, $route);
            $id = $route->parameter($key);
            $repository = $this->getRepository($modelClass);

            $this->applySpecifications($repository, $request, 'show');

            if (null === $result = $repository->find($id)) {
                throw (new ModelNotFoundException())->setModel($modelClass, [$id]);
            }

            $this->withRelations($result, $route->defaults);
        }

        return $this->render($request, $result);
    }

    /**
     * @param Request $request
     *
     * @return Responsable
     */
    public function index(Request $request): Responsable
    {
        $route = $request->route();

        if (null !== $repository = array_get($route->defaults, 'repository.index')) {
            $results = $this->callRepository($request, $repository, 'index');
        } else {
            $modelClass = $this->getModelClass($route);
            $repository = $this->getRepository($modelClass);

            $this->withRelations($repository, $route->defaults);
            $this->applySpecifications($repository, $request, 'index');

            if (true === array_get($route->defaults, 'paginate')) {
                $repository->paginate($request->get('limit', 50));
            }

            $results = $repository->findAll();
        }

        return $this->render($request, $results);
    }

    /**
     * @param Request $request
     *
     * @return Responsable
     * @throws ReflectionException
     */
    public function store(Request $request): Responsable
    {
        $data = $this->getRequestData($request);
        $modelClass = $this->getModelClass($request->route());

        /** @var AbstractModel $model */
        $model = new $modelClass();

        $this->persist($model, $data);

        return $this->render($request, $model);
    }

    /**
     * @param Request $request
     *
     * @return Responsable
     * @throws ReflectionException
     */
    public function update(Request $request): Responsable
    {
        $route = $request->route();
        $data = $this->getRequestData($request);
        $modelClass = $this->getModelClass($route);
        $key = $this->getModelKey($modelClass, $route);

        $model = $modelClass::{'findOrFail'}($route->parameter($key));

        $this->persist($model, $data);

        return $this->render($request, $model);
    }

    /**
     * @param Request $request
     *
     * @return Responsable
     * @throws ReflectionException
     */
    public function destroy(Request $request): Responsable
    {
        $route = $request->route();
        $modelClass = $this->getModelClass($route);
        $key = $this->getModelKey($modelClass, $route);
        $id = $route->parameter($key);
        $repository = $this->getRepository($modelClass);

        if (null === $model = $repository->find($id)) {
            throw (new ModelNotFoundException())->setModel($modelClass, [$id]);
        }

        $repository->remove($model);

        return $this->render($request, $model);
    }

    protected function callRepository(Request $request, array $repo, string $action)
    {
        $route = $request->route();
        $data = collect(array_merge($request->route()->parameters(), $request->all()));
        $args = [];

        list($class, $method) = explode('@', $repo['call']);

        if (isset($repo['arguments']) && is_array($repo['arguments'])) {
            $args = array_values($data->only($repo['arguments'])->all());
        }

        /** @var RepositoryInterface $repo */
        $repo = app($class);

        $this->applySpecifications($repo, $request, $action);

        if (null !== $relations = $this->getRelations($route->defaults)) {
            $repo->with($relations);
        }

        if (true === array_get($route->defaults, 'paginate')) {
            $repo->paginate($request->get('limit', 50));
        }

        return $repo->{$method}(...$args);
    }

    protected function getModelClass(Route $route): string
    {
        if (null !== $resource = array_get($route->defaults, 'resource')) {
            return $this->resourceRegistry()->get($resource)->getModelClass();
        }

        throw new RuntimeException('Parameter "resource" not found');
    }

    protected function getModelKey(string $modelClass, Route $route): string
    {
        $key = Str::snake(substr($modelClass, strrpos($modelClass, '\\') + 1));

        return array_get($route->defaults, 'key', $key);
    }

    /**
     * @param AbstractModel $model
     * @param array         $data
     *
     * @throws ReflectionException
     */
    protected function persist(AbstractModel $model, array $data): void
    {
        $this->appendRelations($model, $data);

        $model->fill($data);

        $repository = $this->getRepository(get_class($model));

        $repository->save($model);
    }

    protected function getRequestData(Request $request): array
    {
        return $this->validateRequest($request);
    }

    protected function appendRelations(AbstractModel $model, array &$data): void
    {
        foreach ($data as $attribute => $value) {

            $method = Str::camel($attribute);

            if (method_exists($model, $method) && $model->{$method}() instanceof Relation) {
                $relation = $model->{$method}();

                if ($relation instanceof BelongsToMany) {
                    $relation->detach();
                    if (!empty($value)) {
                        $relation->attach((array) $value);
                    }
                } else {
                    if (null !== $value) {
                        $relation = $this->findRelatedModel($model->{$method}(), $value);
                    } else {
                        $relation = null;
                    }

                    $model->{$method}()->associate($relation);
                }

                unset($data[$attribute]);
            }
        }
    }

    protected function findRelatedModel(Relation $relation, string $id): AbstractModel
    {
        $class = get_class($relation->getModel());

        return $class::{'findOrFail'}($id);
    }

    protected function applySpecifications(RepositoryInterface $repository, Request $request, string $action)
    {
        $options = $request->route()->defaults;

        if ($specs = (array) array_get($options, sprintf('specs.%s', $action))) {
            foreach ($specs as $spec) {
                $arguments = [];

                if ($specArgs = array_get($spec, 'arguments')) {
                    foreach ($specArgs as $key => $value) {
                        if (is_int($key)) {
                            $arguments[$value] = $request->get($value);
                        } else {
                            $arguments[$key] = $request->get($key, $value);
                        }
                    }
                }

                $repository->match($this->specificationRegistry()->get(array_get($spec, 'name'), $arguments));
            }
        }
    }

    protected function getRepository(string $modelClass): RepositoryInterface
    {
        return $this->entityManager()->getRepository($modelClass);
    }

    /**
     * @param string $modelClass
     *
     * @return Builder
     */
    protected function createQueryBuilder(string $modelClass)
    {
        return $modelClass::{'query'}();
    }

    protected function resourceRegistry(): ResourceRegistryInterface
    {
        return app(ResourceRegistryInterface::class);
    }

    protected function entityManager(): EntityManagerInterface
    {
        return app(EntityManagerInterface::class);
    }

    protected function specificationRegistry(): SpecificationRegistryInterface
    {
        return app(SpecificationRegistryInterface::class);
    }
}
