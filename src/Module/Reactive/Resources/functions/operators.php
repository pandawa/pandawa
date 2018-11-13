<?php
declare(strict_types=1);

namespace Pandawa\Reactive;

use Exception;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Contracts\Support\Arrayable;
use Psr\Http\Message\ResponseInterface;
use Rx\AsyncSchedulerInterface;
use Rx\Observable;
use Rx\ObservableInterface;
use Rx\SchedulerInterface;

function map(callable $project): callable
{
    return function (Observable $source) use ($project) {
        return $source->map($project);
    };
}

function flatMap(callable $project): callable
{
    return function (Observable $source) use ($project) {
        return $source->flatMap($project);
    };
}

function tap(callable $project)
{
    return function (Observable $source) use ($project) {
        return $source->do($project);
    };
}

function merge(ObservableInterface $other)
{
    return function (Observable $source) use ($other) {
        return $source->merge($other);
    };
}

function mergeAll(callable $project)
{
    return function (Observable $source) use ($project) {
        return $source->mergeAll($project);
    };
}

function mapWithIndex(callable $project)
{
    return function (Observable $source) use ($project) {
        return $source->mapWithIndex($project);
    };
}

function mapTo(callable $project)
{
    return function (Observable $source) use ($project) {
        return $source->mapTo($project);
    };
}

function select(callable $project)
{
    return function (Observable $source) use ($project) {
        return $source->select($project);
    };
}

function filter(callable $project)
{
    return function (Observable $source) use ($project) {
        return $source->filter($project);
    };
}

function flatMapTo(ObservableInterface $observable)
{
    return function (Observable $source) use ($observable) {
        return $source->flatMapTo($observable);
    };
}

function flatMapLatest(callable $project)
{
    return function (Observable $source) use ($project) {
        return $source->flatMapLatest($project);
    };
}

function skip(int $count)
{
    return function (Observable $source) use ($count) {
        return $source->skip($count);
    };
}

function skipWhile(callable $project)
{
    return function (Observable $source) use ($project) {
        return $source->skipWhile($project);
    };
}

function skipWhileWithIndex(callable $project)
{
    return function (Observable $source) use ($project) {
        return $source->skipWhileWithIndex($project);
    };
}

function take(int $count)
{
    return function (Observable $source) use ($count) {
        return $source->take($count);
    };
}

function takeUntil(ObservableInterface $other)
{
    return function (Observable $source) use ($other) {
        return $source->takeUntil($other);
    };
}

function takeWhile(callable $project)
{
    return function (Observable $source) use ($project) {
        return $source->takeWhile($project);
    };
}

function takeWhileWithIndex(callable $project)
{
    return function (Observable $source) use ($project) {
        return $source->takeWhileWithIndex($project);
    };
}

function takeLast(int $count)
{
    return function (Observable $source) use ($count) {
        return $source->takeLast($count);
    };
}

function groupBy(callable $keySelector, callable $elementSelector = null, callable $keySerializer = null)
{
    return function (Observable $source) use ($keySelector, $elementSelector, $keySerializer) {
        return $source->groupBy($keySelector, $elementSelector, $keySerializer);
    };
}

function groupByUntil(callable $keySelector, callable $elementSelector = null, callable $keySerializer = null)
{
    return function (Observable $source) use ($keySelector, $elementSelector, $keySerializer) {
        return $source->groupByUntil($keySelector, $elementSelector, $keySerializer);
    };
}

function lift(callable $operatorFactory)
{
    return function (Observable $source) use ($operatorFactory) {
        return $source->lift($operatorFactory);
    };
}

function reduce(callable $accumulator, $seed = null)
{
    return function (Observable $source) use ($accumulator, $seed) {
        return $source->reduce($accumulator, $seed);
    };
}

function distinct(callable $comparer = null)
{
    return function (Observable $source) use ($comparer) {
        return $source->distinct($comparer);
    };
}

function distinctKey(callable $keySelector, callable $comparer = null)
{
    return function (Observable $source) use ($keySelector, $comparer) {
        return $source->distinctKey($keySelector, $comparer);
    };
}

function distinctUntilChanged(callable $comparer = null)
{
    return function (Observable $source) use ($comparer) {
        return $source->distinctUntilChanged($comparer);
    };
}

function distinctUntilKeyChanged(callable $keySelector, callable $comparer = null)
{
    return function (Observable $source) use ($keySelector, $comparer) {
        return $source->distinctUntilKeyChanged($keySelector, $comparer);
    };
}

function tapOnError(callable $onError)
{
    return function (Observable $source) use ($onError) {
        return $source->doOnError($onError);
    };
}

function tapOnComplete(callable $onError)
{
    return function (Observable $source) use ($onError) {
        return $source->doOnCompleted($onError);
    };
}

function scan(callable $accumulator, $seed = null)
{
    return function (Observable $source) use ($accumulator, $seed) {
        return $source->scan($accumulator, $seed);
    };
}

function toArray()
{
    return function (Observable $source) {
        return $source->toArray();
    };
}

function skipLast(int $count)
{
    return function (Observable $source) use ($count) {
        return $source->skipLast($count);
    };
}

function skipUntil(ObservableInterface $other)
{
    return function (Observable $source) use ($other) {
        return $source->skipUntil($other);
    };
}

function asObservable()
{
    return function (Observable $source) {
        return $source->asObservable();
    };
}

function concat(ObservableInterface $observable)
{
    return function (Observable $source) use ($observable) {
        return $source->concat($observable);
    };
}

function concatMap(callable $selector, callable $resultSelector = null)
{
    return function (Observable $source) use ($selector, $resultSelector) {
        return $source->concatMap($selector, $resultSelector);
    };
}

function concatMapTo(ObservableInterface $observable, callable $resultSelector = null)
{
    return function (Observable $source) use ($observable, $resultSelector) {
        return $source->concatMapTo($observable, $resultSelector);
    };
}

function concatAll()
{
    return function (Observable $source) {
        return $source->concatAll();
    };
}

function count(callable $predicate = null)
{
    return function (Observable $source) use ($predicate) {
        return $source->count($predicate);
    };
}

function zip(array $observables, callable $selector = null)
{
    return function (Observable $source) use ($observables, $selector) {
        return $source->zip($observables, $selector);
    };
}

function retry(int $retryCount = -1)
{
    return function (Observable $source) use ($retryCount) {
        return $source->retry($retryCount);
    };
}

function retryWhen(callable $notifier)
{
    return function (Observable $source) use ($notifier) {
        return $source->retryWhen($notifier);
    };
}

function combineLatest(array $observables, callable $selector = null)
{
    return function (Observable $source) use ($observables, $selector) {
        return $source->combineLatest($observables, $selector);
    };
}

function withLatestFrom(array $observables, callable $selector = null)
{
    return function (Observable $source) use ($observables, $selector) {
        return $source->withLatestFrom($observables, $selector);
    };
}

function defaultIfEmpty(ObservableInterface $observable)
{
    return function (Observable $source) use ($observable) {
        return $source->defaultIfEmpty($observable);
    };
}

function repeat(int $count = -1)
{
    return function (Observable $source) use ($count) {
        return $source->repeat($count);
    };
}

function repeatWhen(callable $notifier)
{
    return function (Observable $source) use ($notifier) {
        return $source->repeatWhen($notifier);
    };
}

function delay(int $delay, AsyncSchedulerInterface $scheduler = null)
{
    return function (Observable $source) use ($delay, $scheduler) {
        return $source->delay($delay, $scheduler);
    };
}

function timeout(int $timeout, ObservableInterface $timeoutObservable = null, AsyncSchedulerInterface $scheduler = null)
{
    return function (Observable $source) use ($timeout, $timeoutObservable, $scheduler) {
        return $source->timeout($timeout, $timeoutObservable, $scheduler);
    };
}

function bufferWithCount(int $count, int $skip = null)
{
    return function (Observable $source) use ($count, $skip) {
        return $source->bufferWithCount($count, $skip);
    };
}

function catchError(callable $selector)
{
    return function (Observable $source) use ($selector) {
        return $source->catch($selector);
    };
}

function startWith($startValue, SchedulerInterface $scheduler = null)
{
    return function (Observable $source) use ($startValue, $scheduler) {
        return $source->startWith($startValue, $scheduler);
    };
}

function startWithArray(array $startArray, SchedulerInterface $scheduler = null)
{
    return function (Observable $source) use ($startArray, $scheduler) {
        return $source->startWithArray($startArray, $scheduler);
    };
}

function min(callable $comparer = null)
{
    return function (Observable $source) use ($comparer) {
        return $source->min($comparer);
    };
}

function max(callable $comparer = null)
{
    return function (Observable $source) use ($comparer) {
        return $source->max($comparer);
    };
}

function materialize()
{
    return function (Observable $source) {
        return $source->materialize();
    };
}

function dematerialize()
{
    return function (Observable $source) {
        return $source->dematerialize();
    };
}

function timestamp(SchedulerInterface $scheduler = null)
{
    return function (Observable $source) use ($scheduler) {
        return $source->timestamp($scheduler);
    };
}

function sum()
{
    return function (Observable $source) {
        return $source->sum();
    };
}

function average()
{
    return function (Observable $source) {
        return $source->average();
    };
}

function pluck($property)
{
    return function (Observable $source) use ($property) {
        return $source->pluck($property);
    };
}

function throttle(int $throttleDuration, SchedulerInterface $scheduler = null)
{
    return function (Observable $source) use ($throttleDuration, $scheduler) {
        return $source->throttle($throttleDuration, $scheduler);
    };
}

function isEmpty()
{
    return function (Observable $source) {
        return $source->isEmpty();
    };
}

function complete(callable $selector)
{
    return function (Observable $source) use ($selector) {
        return $source->finally($selector);
    };
}

function compose(callable $compose)
{
    return function (Observable $source) use ($compose) {
        return $source->compose($compose);
    };
}

function debug($message)
{
    return tap(function ($value) use ($message) {
        if (false === config('app.debug')) {
            return;
        }

        if (is_callable($message)) {
            logger($message($value));
        } else {
            if (is_array($value)) {
                $value = json_encode($value);
            } else if ($value instanceof Arrayable) {
                $value = json_encode($value->toArray());
            } else if (is_object($value)) {
                $value = serialize($value);
            }

            logger(str_replace(':value', $value, $message));
        }
    });
}

function responseBody()
{
    return map(function (ResponseInterface $response) {
        return $response->getBody()->getContents();
    });
}

function jsonDecode($asoc = false)
{
    return map(function ($value) use ($asoc) {
        return json_decode($value, $asoc);
    });
}

function throwHttpError()
{
    return catchError(function (Exception $exception) {
        if ($exception instanceof BadResponseException) {
            $obj = json_decode($exception->getResponse()->getBody()->getContents());
            $message = $obj ? $obj->message : $exception->getMessage();
        } else {
            $message = $exception->getMessage();
        }

        throw new Exception($message, $exception->getCode(), $exception);
    });
}
