<?php
declare(strict_types=1);

namespace Pandawa\Module\Reactive\Observable;

use Pandawa\Module\Reactive\Operator\PipeOperatorTrait;
use Rx\Observable\TimerObservable as RxTimerObservable;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class TimerObservable extends RxTimerObservable
{
    use PipeOperatorTrait;
}
