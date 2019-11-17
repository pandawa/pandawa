<?php
declare(strict_types=1);

namespace Pandawa\Component\Transformer;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use Pandawa\Component\Ddd\AbstractModel;
use Pandawa\Component\Ddd\Collection as PandawaCollection;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
abstract class AbstractResourceTransformer implements TransformerInterface
{
    const FIELDS_PARAM = 'fields';
    const INCLUDE_PARAM = 'include';

    /**
     * @param mixed $data
     * @param array $tags
     *
     * @return mixed
     */
    public function transform($data, array $tags = [])
    {
        if ($data instanceof Collection) {
            if (!empty($this->relations()) && !empty($includes = request($this->getKey(self::INCLUDE_PARAM)))) {
                $includes = explode(',', $includes);

                if (!empty($diff = array_diff($includes, $this->getAllKeys($this->relations())))) {
                    throw new InvalidArgumentException(sprintf('Relations "%s" is not defined.', implode(', ', $diff)));
                }

                $data = new PandawaCollection($data);

                $data->load(array_map(function ($include) {
                    if ($rel = $this->relations()[$include] ?? null) {
                        return $rel;
                    }

                    return $include;
                }, $includes));
            }

            return $data;
        }

        $serialized = $this->filterFields($data);

        if ($data instanceof AbstractModel) {
            $serialized = array_merge($serialized, $this->serializeRelations($data));
        }

        return $this->transformSerialized($serialized, $tags);
    }

    /**
     * @return array
     */
    abstract protected function relations(): array;

    /**
     * @param string $key
     *
     * @return string
     */
    protected function getKey(string $key): string
    {
        return sprintf('%s.%s', $key, $this->resource());
    }

    /**
     * @return string
     */
    abstract protected function resource(): string;

    /**
     * @param array $arr
     *
     * @return array
     */
    protected function getAllKeys(array $arr): array
    {
        $keys = [];

        foreach ($arr as $key => $value) {
            if (is_string($key)) {
                $keys[] = $key;

                continue;
            }

            $keys[] = $value;
        }

        return $keys;
    }

    /**
     * @param mixed $data
     *
     * @return array
     */
    protected function filterFields($data): array
    {
        if (empty($this->fields())) {
            return $data;
        }

        $fields = $this->getAllKeys($this->eagerFields());

        if (!empty($requestFields = request($this->getKey(self::FIELDS_PARAM), []))) {
            $fields = explode(',', $requestFields);

            if (!empty($diff = array_diff($fields, $this->getAllKeys($this->fields())))) {
                throw new InvalidArgumentException(sprintf('Fields "%s" is not defined.', implode(', ', $diff)));
            }
        }

        return $this->serializeFields($data, $fields);
    }

    /**
     * @return array
     */
    abstract protected function fields(): array;

    /**
     * @return array
     */
    protected function eagerFields(): array
    {
        return $this->fields();
    }

    /**
     * @param mixed $data
     * @param array $fields
     *
     * @return array
     */
    protected function serializeFields($data, array $fields): array
    {
        $serialized = [];

        foreach ($fields as $field) {
            $key = $this->fields()[$field] ?? $field;

            $serialized[$field] = $this->getValue($data, $key);
        }

        return $serialized;
    }

    /**
     * @param mixed  $data
     * @param string $key
     *
     * @return mixed|null
     */
    protected function getValue($data, string $key)
    {
        if (is_array($data)) {
            return $data[$key] ?? null;
        }

        if (is_object($data)) {
            return $data->{$key} ?? null;
        }

        return null;
    }

    /**
     * @param AbstractModel $model
     *
     * @return array
     */
    protected function serializeRelations($model): array
    {
        if (empty($this->relations())) {
            return [];
        }

        $data = $model->getRelations();
        $relations = $this->eagerRelations();

        if (!empty($includes = request($this->getKey(self::INCLUDE_PARAM), []))) {
            $includes = explode(',', $includes);

            if (!empty($diff = array_diff($includes, $this->getAllKeys($this->relations())))) {
                throw new InvalidArgumentException(sprintf('Relations "%s" is not defined.', implode(', ', $diff)));
            }

            $relations = $includes;
        }

        $loadedRelations = array_keys($model->getRelations());
        $transformedRelations = array_map(function ($include) {
            if ($rel = $this->relations()[$include] ?? null) {
                return $rel;
            }

            return $include;
        }, $relations);

        if (!empty($diff = array_diff($transformedRelations, $loadedRelations))) {
            $model->load($diff);

            $data = $model->getRelations();
        }

        $serialized = [];

        foreach ($relations as $relation) {
            $key = $this->relations()[$relation] ?? $relation;

            $serialized[$relation] = $data[$key];
        }

        return $serialized;
    }

    /**
     * @return array
     */
    protected function eagerRelations(): array
    {
        return [];
    }

    /**
     * @param array $data
     * @param array $tags
     *
     * @return array
     */
    protected function transformSerialized(array $data, array $tags = []): array
    {
        return $data;
    }

    /**
     * @param       $data
     * @param array $tags
     *
     * @return bool
     */
    final public function support($data, array $tags = []): bool
    {
        if ($data instanceof Collection) {
            return $this->supportFor($data->first()) && $this->supportForVersion();
        }

        return $this->supportFor($data, $tags) && $this->supportForVersion();
    }

    /**
     * @param       $data
     * @param array $tags
     *
     * @return bool
     */
    abstract protected function supportFor($data, array $tags = []): bool;

    /**
     * @return bool
     */
    protected function supportForVersion(): bool
    {
        return (int)$this->version() === (int)request('version');
    }

    /**
     * @return mixed
     */
    abstract protected function version();
}
