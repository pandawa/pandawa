<?php
declare(strict_types=1);

namespace Pandawa\Module\Reactive\Observable;

use Pandawa\Module\Reactive\Operator\PipeOperatorTrait;
use Rx\Observable\ErrorObservable as RxErrorObservable;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ErrorObservable extends RxErrorObservable
{
    use PipeOperatorTrait;
}
