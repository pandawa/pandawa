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

namespace Pandawa\Component\Ddd;

use Pandawa\Component\Identifier\IdentifierInterface;
use Pandawa\Component\Identifier\Uuid;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Arr;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class Collection extends EloquentCollection
{
    /**
     * @param IdentifierInterface $identifier
     * @param mixed|null          $default
     *
     * @return AbstractModel|mixed|static
     */
    public function find($identifier, $default = null)
    {
        return Arr::first(
            $this->items,
            function (AbstractModel $model) use ($identifier) {
                $currKey = $model->getKey();

                if ($currKey instanceof Uuid) {
                    return $currKey->equals($identifier);
                }

                return $currKey == $identifier;
            },
            $default
        );
    }
}
