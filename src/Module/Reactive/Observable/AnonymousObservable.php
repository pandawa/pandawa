<?php
declare(strict_types=1);

namespace Pandawa\Module\Reactive\Observable;

use Rx\Observable\AnonymousObservable as RxAnonymousObservable;
use Pandawa\Module\Reactive\Operator\PipeOperatorTrait;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class AnonymousObservable extends RxAnonymousObservable
{
    use PipeOperatorTrait;
}
