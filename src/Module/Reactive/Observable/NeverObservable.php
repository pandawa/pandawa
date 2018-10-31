<?php
declare(strict_types=1);

namespace Pandawa\Module\Reactive\Observable;

use Pandawa\Module\Reactive\Operator\PipeOperatorTrait;
use Rx\Observable\NeverObservable as RxNeverObservable;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class NeverObservable extends RxNeverObservable
{
    use PipeOperatorTrait;
}
