<?php

declare(strict_types=1);

namespace Test\Event;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class PostCreated
{
    public function __construct(public readonly string $title)
    {
    }
}
