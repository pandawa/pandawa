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
use Pandawa\Component\Ddd\Specification\CriteriaSpecification;
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

        if (null !== $repository = array_get($route->defaults, 'repos.show')) {
            $result = $this->callRepository($request, $repository, 'show');
        } else {
            $modelClass = $this->getModelClass($route);
            $key = $this->getModelKey($modelClass, $route);
            $id = $route->parameter($key);
            $repository = $this->getRepository($modelClass);

            $this->applySpecifications($repository, $request, 'show');
            $this->applyCriteria($repository, $request);

            if (null === $result = $repository->find($id)) {
                throw (new ModelNotFoundException())->setModel($modelClass, [$id]);
            }

            $this->withRelations($result, $route->defaults, 'show');
        }

        return $this->render($request, $result, (array) array_get($route->defaults, 'trans.show', []));
    }

    /**
     * @param Request $request
     *
     * @return Responsable
     */
    public function index(Request $request): Responsable
    {
        $route = $request->route();

        if (null !== $repository = array_get($route->defaults, 'repos.index')) {
            $results = $this->callRepository($request, $repository, 'index');
        } else {
            $modelClass = $this->getModelClass($route);
            $repository = $this->getRepository($modelClass);

            $this->withRelations($repository, $route->defaults, 'index');
            $this->applySpecifications($repository, $request, 'index');
            $this->applyCriteria($repository, $request);

            if (true === array_get($route->defaults, 'paginate')) {
                $repository->paginate((int) $request->get('limit', 50));
            }

            $results = $repository->findAll();
        }

        return $this->render($request, $results, (array) array_get($route->defaults, 'trans.index', []));
    }

    /**
     * @param Request $request
     *
     * @return Responsable
     * @throws ReflectionException
     */
    public function store(Request $request): Responsable
    {
        $route = $request->route();
        $data = $this->getRequestData($request, 'store');
        $modelClass = $this->getModelClass($route);

        /** @var AbstractModel $model */
        $model = new $modelClass();

        $this->persist($model, $data);
        $this->withRelations($model, $route->defaults, 'store');

        return $this->render($request, $model, (array) array_get($route->defaults, 'trans.store', []));
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
        $data = $this->getRequestData($request, 'update');
        $modelClass = $this->getModelClass($route);
        $key = $this->getModelKey($modelClass, $route);
        $id = $route->parameter($key);
        $repository = $this->getRepository($modelClass);

        $this->applySpecifications($repository, $request, 'update');
        $this->applyCriteria($repository, $request);

        if (null === $model = $repository->find($id)) {
            throw (new ModelNotFoundException())->setModel($modelClass, [$id]);
        }

        $this->persist($model, $data);
        $this->withRelations($model, $route->defaults, 'update');

        return $this->render($request, $model, (array) array_get($route->defaults, 'trans.update', []));
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

        $this->applySpecifications($repository, $request, 'update');
        $this->applyCriteria($repository, $request);

        if (null === $model = $repository->find($id)) {
            throw (new ModelNotFoundException())->setModel($modelClass, [$id]);
        }

        $repository->remove($model);

        return $this->render($request, $model, (array) array_get($route->defaults, 'trans.destroy', []));
    }

    protected function callRepository(Request $request, array $repo, string $action)
    {
        $route = $request->route();
        $modelClass = $this->getModelClass($route);
        $data = collect($this->getAllData($request));
        $args = [];

        $method = $repo['call'];

        if (isset($repo['arguments']) && is_array($repo['arguments'])) {
            $args = array_values($data->only($repo['arguments'])->all());
        }

        $repo = $this->getRepository($modelClass);

        $this->applySpecifications($repo, $request, $action);
        $this->applyCriteria($repo, $request);
        $this->withRelations($repo, $route->defaults, $action);

        if (true === array_get($route->defaults, 'paginate')) {
            $repo->paginate((int) $request->get('limit', 50));
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

    /**
     * @param Request $request
     * @param string  $action
     *
     * @return array
     */
    protected function getRequestData(Request $request, string $action): array
    {
        return $this->validateRequest($request, $action);
    }

    /**
     * @param AbstractModel $model
     * @param array         $data
     *
     * @throws ReflectionException
     */
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

    /**
     * @param Relation $relation
     * @param mixed    $value
     *
     * @return AbstractModel
     * @throws ReflectionException
     */
    protected function findRelatedModel(Relation $relation, $value): AbstractModel
    {
        $class = get_class($relation->getModel());

        if (is_array($value)) {
            $key = $relation->getModel()->getKeyName();

            if (null !== $id = array_get($value, $key)) {
                $model = $class::{'findOrFail'}($id);
                $this->persist($model, array_except($value, $key));
            } else {
                $model = new $class();
                $this->persist($model, $value);
            }
        } else {
            $model = $class::{'findOrFail'}($value);
        }

        return $model;
    }

    protected function applySpecifications(RepositoryInterface $repository, Request $request, string $action): void
    {
        $options = $request->route()->defaults;

        if ($specs = (array) array_get($options, sprintf('specs.%s', $action))) {
            $data = collect($this->getAllData($request));
            foreach ($specs as $spec) {
                $arguments = [];

                if ($specArgs = array_get($spec, 'arguments')) {
                    foreach ($specArgs as $key => $value) {
                        if (is_int($key)) {
                            $arguments[Str::camel($value)] = array_get($data, $value);
                        } else {
                            $arguments[Str::camel($key)] = array_get($data, $key, $value);
                        }
                    }
                }

                $repository->match($this->specificationRegistry()->get(array_get($spec, 'name'), $arguments));
            }
        }
    }

    protected function applyCriteria(RepositoryInterface $repository, Request $request): void
    {
        $route = $request->route();

        if (!empty($criterias = (array) array_get($route->defaults, 'criteria'))) {
            $modelClass = $repository->getModelClass();
            $model = new $modelClass();
            $filters = [];

            foreach ($criterias as $criteria) {
                if (is_array($criteria)) {
                    $key = array_get($criteria, 'target');
                    $criteria = array_get($criteria, 'source');
                } else {
                    $key = $criteria;
                }

                $relation = Str::camel($criteria);

                if (method_exists($model, $relation)) {
                    $relation = $model->{$relation}();

                    if (method_exists($relation, 'getForeignKey')) {
                        $key = $relation->getForeignKey();
                    }
                }

                $filters[$key] = $route->parameter($criteria, $request->get($criteria));
            }

            $repository->match(new CriteriaSpecification($filters));
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
