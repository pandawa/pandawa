<?php
declare(strict_types=1);

namespace Pandawa\Reactive;

use function foo\func;
use Illuminate\Support\Collection;
use Iterator;
use Pandawa\Module\Reactive\Observable\AnonymousObservable;
use Pandawa\Module\Reactive\Observable\ArrayObservable;
use Pandawa\Module\Reactive\Observable\EmptyObservable;
use Pandawa\Module\Reactive\Observable\ErrorObservable;
use Pandawa\Module\Reactive\Observable\ForkJoinObservable;
use Pandawa\Module\Reactive\Observable\HttpClientObservable;
use Pandawa\Module\Reactive\Observable\IntervalObservable;
use Pandawa\Module\Reactive\Observable\IteratorObservable;
use Pandawa\Module\Reactive\Observable\NeverObservable;
use Pandawa\Module\Reactive\Observable\ObservableFactory;
use Pandawa\Module\Reactive\Observable\RangeObservable;
use Pandawa\Module\Reactive\Observable\ReturnObservable;
use React\Promise\PromiseInterface;
use Rx\AsyncSchedulerInterface;
use Rx\Observable;
use Rx\SchedulerInterface;

function fromArray(array $values, SchedulerInterface $scheduler = null): ArrayObservable
{
    return ObservableFactory::fromArray($values, $scheduler);
}

function of($value, SchedulerInterface $scheduler = null): ReturnObservable
{
    return ObservableFactory::of($value, $scheduler);
}

function forkJoin(array $observables = [], callable $resultSelector = null): ForkJoinObservable
{
    return ObservableFactory::forkJoin($observables, $resultSelector);
}

function fromIterator(Iterator $values, SchedulerInterface $scheduler = null): IteratorObservable
{
    return ObservableFactory::fromIterator($values, $scheduler);
}

function fromCollection(Collection $collection, SchedulerInterface $scheduler = null): IteratorObservable {
    return ObservableFactory::fromIterator($collection->getIterator(), $scheduler);
}

function create(callable $subscribeAction): AnonymousObservable
{
    return ObservableFactory::create($subscribeAction);
}

function fromEmpty(SchedulerInterface $scheduler = null): EmptyObservable
{
    return ObservableFactory::empty($scheduler);
}

function error(\Throwable $error, SchedulerInterface $scheduler = null): ErrorObservable
{
    return ObservableFactory::error($error, $scheduler);
}

function interval(int $interval, AsyncSchedulerInterface $scheduler = null): IntervalObservable
{
    return ObservableFactory::interval($interval, $scheduler);
}

function never(): NeverObservable
{
    return ObservableFactory::never();
}

/**
 * @param callable                $factory
 * @param SchedulerInterface|null $scheduler
 *
 * @return Observable|EmptyObservable
 * @throws \Exception
 */
function defer(callable $factory, SchedulerInterface $scheduler = null)
{
    return ObservableFactory::defer($factory, $scheduler);
}

function range(int $start, int $count, SchedulerInterface $scheduler = null): RangeObservable
{
    return ObservableFactory::range($start, $count, $scheduler);
}

function fromPromise(PromiseInterface $promise)
{
    return ObservableFactory::fromPromise($promise);
}

function httpRequest(string $uri, string $method = 'GET', array $options = [], SchedulerInterface $scheduler = null): HttpClientObservable
{
    return ObservableFactory::httpRequest($uri, $method, $options, $scheduler);
}
