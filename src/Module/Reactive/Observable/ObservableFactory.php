<?php
declare(strict_types=1);

namespace Pandawa\Module\Reactive\Observable;

use Pandawa\Module\Reactive\Operator\PipeOperatorTrait;
use React\Promise\CancellablePromiseInterface;
use React\Promise\PromiseInterface;
use Rx\AsyncSchedulerInterface;
use Rx\Disposable\CallbackDisposable;
use Rx\Observable;
use Rx\ObservableFactoryWrapper;
use Rx\Operator\DeferOperator;
use Rx\React\RejectedPromiseException;
use Rx\Scheduler;
use Rx\SchedulerInterface;
use Rx\Subject\AsyncSubject;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ObservableFactory
{
    /**
     * @param array                   $array
     * @param SchedulerInterface|null $scheduler
     *
     * @return ArrayObservable
     * @throws \Exception
     */
    public static function fromArray(array $array, SchedulerInterface $scheduler = null): ArrayObservable
    {
        return new ArrayObservable($array, $scheduler ?: Scheduler::getDefault());
    }

    /**
     * @param array         $observables
     * @param callable|null $resultSelector
     *
     * @return ForkJoinObservable
     */
    public static function forkJoin(array $observables = [], callable $resultSelector = null): ForkJoinObservable
    {
        return new ForkJoinObservable($observables, $resultSelector);
    }

    /**
     * @param                         $value
     * @param SchedulerInterface|null $scheduler
     *
     * @return ReturnObservable
     * @throws \Exception
     */
    public static function of($value, SchedulerInterface $scheduler = null): ReturnObservable
    {
        return new ReturnObservable($value, $scheduler ?: Scheduler::getDefault());
    }

    /**
     * @param callable $subscribeAction
     *
     * @return AnonymousObservable
     */
    public static function create(callable $subscribeAction): AnonymousObservable
    {
        return new AnonymousObservable($subscribeAction);
    }

    /**
     * @param int                          $interval
     * @param AsyncSchedulerInterface|null $scheduler
     *
     * @return IntervalObservable
     * @throws \Exception
     */
    public static function interval(int $interval, AsyncSchedulerInterface $scheduler = null): IntervalObservable
    {
        return new IntervalObservable($interval, $scheduler ?: Scheduler::getAsync());
    }

    /**
     * @param SchedulerInterface|null $scheduler
     *
     * @return EmptyObservable
     * @throws \Exception
     */
    public static function empty(SchedulerInterface $scheduler = null): EmptyObservable
    {
        return new EmptyObservable($scheduler ?: Scheduler::getDefault());
    }

    /**
     * @return NeverObservable
     */
    public static function never(): NeverObservable
    {
        return new NeverObservable();
    }

    /**
     * @param \Iterator               $iterator
     * @param SchedulerInterface|null $scheduler
     *
     * @return IteratorObservable
     * @throws \Exception
     */
    public static function fromIterator(\Iterator $iterator, SchedulerInterface $scheduler = null): IteratorObservable
    {
        return new IteratorObservable($iterator, $scheduler ?: Scheduler::getDefault());
    }

    /**
     * @param \Throwable              $error
     * @param SchedulerInterface|null $scheduler
     *
     * @return ErrorObservable
     */
    public static function error(\Throwable $error, SchedulerInterface $scheduler = null): ErrorObservable
    {
        return new ErrorObservable($error, $scheduler ?: Scheduler::getImmediate());
    }

    /**
     * @param callable                $factory
     * @param SchedulerInterface|null $scheduler
     *
     * @return Observable
     * @throws \Exception
     */
    public static function defer(callable $factory, SchedulerInterface $scheduler = null): Observable
    {
        return static::empty($scheduler)
            ->lift(function () use ($factory) {
                return new DeferOperator(new ObservableFactoryWrapper($factory));
            });
    }

    /**
     * @param int                     $start
     * @param int                     $count
     * @param SchedulerInterface|null $scheduler
     *
     * @return RangeObservable
     * @throws \Exception
     */
    public static function range(int $start, int $count, SchedulerInterface $scheduler = null): RangeObservable
    {
        return new RangeObservable($start, $count, $scheduler ?: Scheduler::getDefault());
    }

    /**
     * @param int                          $dueTime
     * @param AsyncSchedulerInterface|null $scheduler
     *
     * @return TimerObservable
     * @throws \Exception
     */
    public static function timer(int $dueTime, AsyncSchedulerInterface $scheduler = null): TimerObservable
    {
        return new TimerObservable($dueTime, $scheduler ?: Scheduler::getAsync());
    }

    /**
     * @param PromiseInterface $promise
     *
     * @return Observable|PipeOperatorTrait
     */
    public static function fromPromise(PromiseInterface $promise): Observable
    {
        $subject = new AsyncSubject();

        $p = $promise->then(
            function ($value) use ($subject) {
                $subject->onNext($value);
                $subject->onCompleted();
            },
            function ($error) use ($subject) {
                $error = $error instanceof \Exception ? $error : new RejectedPromiseException($error);
                $subject->onError($error);
            }
        );

        return new AnonymousObservable(function ($observer) use ($subject, $p) {
            $disp = $subject->subscribe($observer);
            return new CallbackDisposable(function () use ($p, $disp) {
                $disp->dispose();
                if ($p instanceof CancellablePromiseInterface) {
                    $p->cancel();
                }
            });
        });
    }

    /**
     * @param string                  $uri
     * @param string                  $method
     * @param array                   $options
     * @param SchedulerInterface|null $scheduler
     *
     * @return HttpClientObservable
     * @throws \Exception
     */
    public static function httpRequest(string $uri, string $method, array $options = [], SchedulerInterface $scheduler = null): HttpClientObservable
    {
        return new HttpClientObservable($uri, $method, $options, $scheduler ?: Scheduler::getDefault());
    }
}
