<?php

declare(strict_types=1);

namespace Pandawa\Component\Eloquent\Traits;

use Carbon\Carbon;
use DateTimeInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait FormatDateTrait
{
    protected function serializeDate(DateTimeInterface $date): string
    {
        if ($date instanceof Carbon && $date->isStartOfDay()) {
            return $date->format('Y-m-d');
        }

        return $date->format('Y-m-d H:i:s');
    }
}
