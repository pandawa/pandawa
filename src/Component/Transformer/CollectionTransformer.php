<?php
declare(strict_types=1);

namespace Pandawa\Component\Transformer;

use Illuminate\Support\Collection;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class CollectionTransformer implements TransformerInterface
{
    /**
     * @param Collection $data
     * @param array      $tags
     *
     * @return mixed
     */
    public function transform($data, array $tags = [])
    {
        return $data->map(
            function ($item) use ($tags) {
                return $this->transformerRegistry()->transform($item, $tags);
            }
        );
    }

    /**
     * @return TransformerRegistryInterface
     */
    private function transformerRegistry(): TransformerRegistryInterface
    {
        return app(TransformerRegistryInterface::class);
    }

    /**
     * @param mixed $data
     * @param array $tags
     *
     * @return bool
     */
    public function support($data, array $tags = []): bool
    {
        return $data instanceof Collection;
    }
}
