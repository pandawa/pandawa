<?php
declare(strict_types=1);

namespace Pandawa\Module\Reactive\Observable;

use Pandawa\Module\Reactive\Operator\PipeOperatorTrait;
use Rx\Observable\IteratorObservable as RxIteratorObservable;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class IteratorObservable extends RxIteratorObservable
{
    use PipeOperatorTrait;
}
