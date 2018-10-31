<?php
declare(strict_types=1);

namespace Pandawa\Module\Reactive\Operator;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait PipeOperatorTrait
{
    /**
     * @param callable ...$args
     *
     * @return $this
     */
    public function pipe(callable ...$args)
    {
        if (empty($args)) {
            return $this;
        }

        return $this->pipeFromArray($args)($this);
    }

    /**
     * @param array $args
     *
     * @return callable
     */
    private function pipeFromArray(array $args): callable
    {
        if (1 === count($args)) {
            return $args[0];
        }

        return function ($input) use ($args) {
            return array_reduce(
                $args,
                function($prev, callable $fn) {
                    return $fn($prev);
                },
                $input
            );
        };
    }
}
