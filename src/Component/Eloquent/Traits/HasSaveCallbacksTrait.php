<?php

declare(strict_types=1);

namespace Pandawa\Component\Eloquent\Traits;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait HasSaveCallbacksTrait
{
    protected array $saveCallbacks = [
        'before' => [],
        'after'  => [],
    ];

    public static function bootHasSaveCallbacksTrait(): void
    {
        static::saving(function ($model) {
            $model->fireBeforeSaveCallbacks();
        });

        static::saved(function ($model) {
            $model->fireAfterSaveCallbacks();
        });
    }

    public function addBeforeSaveCallback(callable $callback): void
    {
        $this->saveCallbacks['before'][] = $callback;
    }

    public function addAfterSaveCallback(callable $callback): void
    {
        $this->saveCallbacks['after'][] = $callback;
    }

    public function fireBeforeSaveCallbacks(): void
    {
        $this->fireCallbacks('before');
    }

    public function fireAfterSaveCallbacks(): void
    {
        $this->fireCallbacks('after');
    }

    protected function fireCallbacks(string $type): void
    {
        $callbacks = $this->saveCallbacks[$type];

        $this->saveCallbacks[$type] = [];

        foreach ($callbacks as $callback) {
            $callback($this);
        }
    }
}
