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

namespace Pandawa\Component\Ddd\Relationship;

use Illuminate\Database\Eloquent\Relations\BelongsTo as LaravelBelongsTo;
use Pandawa\Component\Ddd\AbstractModel;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class BelongsTo extends LaravelBelongsTo
{
    /**
     * @var AbstractModel
     */
    protected $parent;

    /**
     * {@inheritdoc}
     */
    public function associate($model)
    {
        if ($model instanceof AbstractModel) {
            $this->parent->addBeforeAction(
                function () use ($model) {
                    $this->child->setAttribute($this->foreignKey, $model->getAttribute($this->ownerKey));
                }
            );

            $this->child->setRelation($this->relation, $model);

            return $this->child;
        }

        return parent::associate($model);
    }
}
