<?php

declare(strict_types=1);

namespace Pandawa\Component\Eloquent;

use Illuminate\Cache\TaggedCache;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Pandawa\Contracts\Eloquent\Cache\CacheHandlerInterface;
use Pandawa\Contracts\Eloquent\QueryBuilderInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class CacheHandler implements CacheHandlerInterface
{
    const MODELS = 'models';
    const QUERIES = 'queries';

    public function __construct(
        protected readonly CacheRepository $repository,
        protected readonly ?int $ttl = 60 * 60 * 24,
    ) {
    }

    public function rememberModel(Eloquent $model): Eloquent
    {
        return $this->getCacheTags($model, static::MODELS)->remember(
            $model->getKey(),
            $this->ttl,
            fn() => $model,
        );
    }

    public function rememberQuery(QueryBuilderInterface $queryBuilder, mixed $value): mixed
    {
        return $this->getCacheTags($queryBuilder->getModel(), static::QUERIES)->remember(
            $this->makeKeyFromQueryBuilder($queryBuilder),
            $this->ttl,
            fn() => $value,
        );
    }

    public function getByKey(Eloquent $model, int|string $key): ?Eloquent
    {
        return $this->getCacheTags($model, static::MODELS)->get($key);
    }

    public function getByQuery(QueryBuilderInterface $queryBuilder): mixed
    {
        return $this->getCacheTags($queryBuilder->getModel(), static::QUERIES)->get(
            $this->makeKeyFromQueryBuilder($queryBuilder),
        );
    }

    public function invalidate(Eloquent $model): Eloquent
    {
        $this->getCacheTags($model, static::MODELS)->delete($model->getKey());
        $this->getCacheTags($model, static::QUERIES)->flush();

        return $model;
    }

    protected function makeKeyFromQueryBuilder(QueryBuilderInterface $queryBuilder): string
    {
        $parts = [
            $queryBuilder->toSql(),
            implode('-', $queryBuilder->getBindings()),
        ];

        return base64_encode(implode('.', $parts));
    }

    protected function getTagName(Eloquent $model, string $type): string
    {
        return $model->getTable().'.'.$type;
    }

    protected function getCacheTags(Eloquent $model, string $type): TaggedCache
    {
        return $this->repository->tags($this->getTagName($model, $type));
    }
}
