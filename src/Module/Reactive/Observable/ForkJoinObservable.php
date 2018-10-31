<?php
declare(strict_types=1);

namespace Pandawa\Module\Reactive\Observable;

use Rx\Observable\ForkJoinObservable as RxForkJoinObservable;
use Pandawa\Module\Reactive\Operator\PipeOperatorTrait;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ForkJoinObservable extends RxForkJoinObservable
{
    use PipeOperatorTrait;
}
