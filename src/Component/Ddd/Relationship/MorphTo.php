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

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo as LaravelMorphTo;
use Pandawa\Component\Ddd\AbstractModel;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class MorphTo extends LaravelMorphTo
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
                    $this->parent->setAttribute(
                        $this->foreignKey, $model instanceof Model ? $model->getKey() : null
                    );

                    $this->parent->setAttribute(
                        $this->morphType, $model instanceof Model ? $model->getMorphClass() : null
                    );
                }
            );

            return $this->parent->setRelation($this->relation, $model);
        }

        return parent::associate($model);
    }

    public function createModelByType($type)
    {
        $class = call_user_func_array([$this->parent, 'getActualClassNameForMorph'], [$type]);

        return new $class;
    }
}
