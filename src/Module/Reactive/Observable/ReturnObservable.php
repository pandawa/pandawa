<?php
declare(strict_types=1);

namespace Pandawa\Module\Reactive\Observable;

use Rx\Observable\ReturnObservable as RxReturnObservable;
use Pandawa\Module\Reactive\Operator\PipeOperatorTrait;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ReturnObservable extends RxReturnObservable
{
    use PipeOperatorTrait;
}
