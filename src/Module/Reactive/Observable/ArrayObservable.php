<?php
declare(strict_types=1);

namespace Pandawa\Module\Reactive\Observable;

use Rx\Observable\ArrayObservable as RxArrayObservable;
use Pandawa\Module\Reactive\Operator\PipeOperatorTrait;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ArrayObservable extends RxArrayObservable
{
    use PipeOperatorTrait;
}
