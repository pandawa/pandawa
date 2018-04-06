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

namespace Pandawa\Component\Transformer;

use Illuminate\Database\Eloquent\Model;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ModelTransformer implements TransformerInterface
{
    /**
     * @param Model $data
     * @param array $tags
     *
     * @return array
     */
    public function transform($data, array $tags = [])
    {
        return array_merge(
            $data->attributesToArray(),
            $data->getRelations()
        );
    }

    /**
     * @param mixed $data
     * @param array $tags
     *
     * @return bool
     */
    public function support($data, array $tags = []): bool
    {
        return $data instanceof Model;
    }
}
