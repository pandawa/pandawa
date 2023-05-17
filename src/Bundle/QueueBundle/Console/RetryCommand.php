<?php

declare(strict_types=1);

namespace Pandawa\Bundle\QueueBundle\Console;

use Carbon\CarbonImmutable;
use DateTimeInterface;
use Illuminate\Contracts\Encryption\Encrypter;
use Pandawa\Annotations\Console\AsConsole;
use Illuminate\Queue\Console\RetryCommand as LaravelRetryCommand;

/**
 * @author  Aldi Arief <aldiarief598@gmail.com>
 */
#[AsConsole]
class RetryCommand extends LaravelRetryCommand
{
    protected function refreshRetryUntil($payload)
    {
        $payload = json_decode($payload, true);

        if (! isset($payload['data']['command'])) {
            return json_encode($payload);
        }

        if (is_string($payload['data']['command']) && str_starts_with($payload['data']['command'], 'O:')) {
            $instance = unserialize($payload['data']['command']);
        } elseif (($payload['data']['encrypted'] ?? false) && $this->laravel->bound(Encrypter::class)) {
            $instance = unserialize($this->laravel->make(Encrypter::class)->decrypt($payload['data']['command']));
        }

        if (isset($instance) && is_object($instance) && ! $instance instanceof \__PHP_Incomplete_Class && method_exists($instance, 'retryUntil')) {
            $retryUntil = $instance->retryUntil();

            $payload['retryUntil'] = $retryUntil instanceof DateTimeInterface
                ? $retryUntil->getTimestamp()
                : $retryUntil;
        } else {
            $retryUntil = $payload['retryUntil'] ?? $payload['timeoutAt'] ?? null;

            $pushedAt = $payload['pushedAt'] ?? microtime(true);

            $payload['retryUntil'] = $retryUntil
                ? CarbonImmutable::now()->addSeconds((int) ceil($retryUntil - $pushedAt))->getTimestamp()
                : null;
        }

        return json_encode($payload);
    }
}