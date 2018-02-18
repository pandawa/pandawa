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

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Route;
use Illuminate\Support\Str;
use Pandawa\Component\Ddd\AbstractModel;
use Pandawa\Component\Ddd\AbstractRepository;
use Pandawa\Component\Resource\ResourceRegistryInterface;
use Pandawa\Component\Validation\RequestValidationTrait;
use ReflectionObject;
use RuntimeException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ResourceController extends Controller implements ResourceControllerInterface
{
    use InteractsWithRelationsTrait, InteractsWithTransformerTrait, RequestValidationTrait;

    public function show(Request $request)
    {
        $route = $request->route();

        if (null !== $repository = array_get($route->defaults, 'repository.show')) {
            $result = $this->callRepository($request, $repository);
        } else {
            $modelClass = $this->getModelClass($route);
            $key = $this->getModelKey($modelClass, $route);
            $result = $modelClass::{'findOrFail'}($route->parameter($key));

            $this->withRelations($result, $route->defaults);
        }

        return $this->transform($result);
    }

    public function index(Request $request)
    {
        $route = $request->route();

        if (null !== $repository = array_get($route->defaults, 'repository.index')) {
            $results = $this->callRepository($request, $repository);
        } else {
            $modelClass = $this->getModelClass($route);
            $stmt = $this->createQueryBuilder($modelClass);

            $this->withRelations($stmt, $route->defaults);

            if (true !== array_get($route->defaults, 'paginate')) {
                $results = $stmt->get();
            } else {
                $results = $stmt->paginate($request->get('limit', 50));
            }
        }

        return $this->transform($results);
    }

    public function store(Request $request)
    {
        $data = $this->getRequestData($request);
        $modelClass = $this->getModelClass($request->route());

        /** @var AbstractModel $model */
        $model = new $modelClass();

        $this->persist($model, $data);

        return $this->transform($model);
    }

    public function update(Request $request)
    {
        $route = $request->route();
        $data = $this->getRequestData($request);
        $modelClass = $this->getModelClass($route);
        $key = $this->getModelKey($modelClass, $route);

        $model = $modelClass::{'findOrFail'}($route->parameter($key));

        $this->persist($model, $data);

        return $this->transform($model);
    }

    public function destroy(Request $request)
    {
        $route = $request->route();
        $modelClass = $this->getModelClass($route);
        $key = $this->getModelKey($modelClass, $route);
        $model = $modelClass::{'findOrFail'}($route->parameter($key));

        $reflection = new ReflectionObject($model);
        $persist = $reflection->getMethod('remove');
        $persist->setAccessible(true);
        $persist->invoke($model);

        return $this->transform($model);
    }

    protected function callRepository(Request $request, array $repo)
    {
        $route = $request->route();
        $data = collect(array_merge($request->route()->parameters(), $request->all()));
        $args = [];

        list($class, $method) = explode('@', $repo['call']);

        if (isset($repo['arguments']) && is_array($repo['arguments'])) {
            $args = array_values($data->only($repo['arguments'])->all());
        }

        /** @var AbstractRepository $repo */
        $repo = app($class);

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
            return $this->registry()->get($resource)->getModelClass();
        }

        throw new RuntimeException('Parameter "resource" not found');
    }

    protected function getModelKey(string $modelClass, Route $route): string
    {
        $key = Str::snake(substr($modelClass, strrpos($modelClass, '\\') + 1));

        return array_get($route->defaults, 'key', $key);
    }

    protected function persist(AbstractModel $model, array $data): void
    {
        $this->appendRelations($model, $data);

        $model->fill($data);

        $reflection = new ReflectionObject($model);
        $persist = $reflection->getMethod('persist');
        $persist->setAccessible(true);
        $persist->invoke($model);
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

    /**
     * @param string $modelClass
     *
     * @return Builder
     */
    protected function createQueryBuilder(string $modelClass)
    {
        return $modelClass::{'query'}();
    }

    protected function registry(): ResourceRegistryInterface
    {
        return app(ResourceRegistryInterface::class);
    }
}
