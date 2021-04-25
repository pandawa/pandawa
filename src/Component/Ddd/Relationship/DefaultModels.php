<?php

declare(strict_types=1);

namespace Pandawa\Component\Ddd\Relationship;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait DefaultModels
{
    protected function getDefaultFor($parent)
    {
        if (!$this->withDefault) {
            return;
        }

        $instance = $this->newRelatedInstanceFor($parent);

        if (is_callable($this->withDefault)) {
            return call_user_func($this->withDefault, $instance, $parent) ?: $instance;
        }

        if (is_array($this->withDefault)) {
            $instance->forceFill($this->withDefault);
        }

        return $instance;
    }
}
