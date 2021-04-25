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
use Pandawa\Component\Ddd\Model;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class BelongsTo extends LaravelBelongsTo
{
    use DefaultModels;

    /**
     * @var AbstractModel
     */
    protected $parent;

    /**
     * {@inheritDoc}
     */
    public function getResults()
    {
        if (is_null($this->child->{$this->foreignKey})) {
            return $this->getDefaultFor($parent);
        }

        return $this->query->first() ?: $this->getDefaultFor($parent);
    }

    /**
     * {@inheritdoc}
     */
    public function associate($model)
    {
        if ($model instanceof Model) {
            $this->parent->addBeforeAction(
                function () use ($model) {
                    $this->child->setAttribute($this->foreignKey, $model->getAttribute($this->ownerKey));
                }
            );

            $this->child->setRelation($this->getRelationName(), $model);

            return $this->child;
        }

        return parent::associate($model);
    }
}
