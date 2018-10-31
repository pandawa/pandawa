<?php
declare(strict_types=1);

namespace Pandawa\Module\Reactive\Observable;

use Rx\Observable\IntervalObservable as RxIntervalObservable;
use Pandawa\Module\Reactive\Operator\PipeOperatorTrait;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class IntervalObservable extends RxIntervalObservable
{
    use PipeOperatorTrait;
}
