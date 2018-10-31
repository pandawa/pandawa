<?php
declare(strict_types=1);

namespace Pandawa\Module\Reactive\Observable;

use Pandawa\Module\Reactive\Operator\PipeOperatorTrait;
use Rx\Observable\RangeObservable as RxRangeObservable;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class RangeObservable extends RxRangeObservable
{
    use PipeOperatorTrait;
}
