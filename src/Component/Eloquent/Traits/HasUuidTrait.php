<?php

declare(strict_types=1);

namespace Pandawa\Component\Eloquent\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait HasUuidTrait
{
    public static function bootHasUuidTrait(): void
    {
        static::creating(function (Model $model) {
            $key = $model->getUuidKeyName();

            if ($model->isAsPrimaryKey()) {
                $model->setIncrementing(false);
            }

            $model->mergeCasts([$key => 'string']);

            $model->setAttribute($key, (string) Str::uuid());
        });
    }

    public function getUuidKeyName(): string
    {
        if (property_exists($this, 'uuidKeyName')) {
            return $this->{'uuidKeyName'};
        }

        return $this->getKeyName();
    }

    public function isAsPrimaryKey(): bool
    {
        return $this->getKeyName() === $this->getUuidKeyName();
    }
}
