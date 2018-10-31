<?php
declare(strict_types=1);

namespace Pandawa\Module\Reactive\Observable;

use Pandawa\Module\Reactive\Operator\PipeOperatorTrait;
use Rx\Observable\EmptyObservable as RxEmptyObservable;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class EmptyObservable extends RxEmptyObservable
{
    use PipeOperatorTrait;
}
