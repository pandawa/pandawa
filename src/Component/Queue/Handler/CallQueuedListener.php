<?php

declare(strict_types=1);

namespace Pandawa\Component\Queue\Handler;

use Illuminate\Events\CallQueuedListener as LaravelCallQueuedListener;
use Pandawa\Contracts\Bus\Envelope;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class CallQueuedListener extends LaravelCallQueuedListener
{
    protected function prepareData(): void
    {
        parent::prepareData();

        $this->data = $this->normalizeData($this->data);
    }

    protected function normalizeData(mixed $data): mixed
    {
        if (is_array($data)) {
            return array_map(
                fn(mixed $value) => $this->normalizeData($value),
                $data
            );
        }

        if ($data instanceof Envelope) {
            return $data->message;
        }

        return $data;
    }
}
