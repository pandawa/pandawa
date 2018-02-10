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

use Pandawa\Component\Identifier\Uuid;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait ModelUuidTrait
{
    public static function bootModelUuidTrait()
    {
        static::creating(function(AbstractModel $model) {
            $model->incrementing = false;
            if (null === $model->{$model->getKeyName()}) {
                $model->{$model->getKeyName()} = Uuid::generate();
            }
        });
    }
}
