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

namespace Pandawa\Module\Api\Transformer;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Transformer extends AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public static function collection($resource): CollectionTransformer
    {
        return new class($resource, get_called_class()) extends CollectionTransformer {
            /**
             * @var string
             */
            public $collects;

            /**
             * Create a new anonymous resource collection.
             *
             * @param  mixed  $resource
             * @param  string  $collects
             */
            public function __construct($resource, $collects)
            {
                $this->collects = $collects;

                parent::__construct($resource);
            }
        };
    }

    /**
     * {@inheritdoc}
     */
    public function toArray($request): array
    {
        if ($this->resource instanceof Arrayable) {
            return $this->resource->toArray();
        }

        return $this->resource;
    }

}
