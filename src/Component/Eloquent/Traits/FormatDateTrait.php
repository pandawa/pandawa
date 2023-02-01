<?php

declare(strict_types=1);

namespace Pandawa\Component\Eloquent\Traits;

use DateTimeInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait FormatDateTrait
{
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
