<?php

declare(strict_types=1);

namespace Test\Bus\Command;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class CreateComment
{
    public function __construct(public readonly string $title)
    {
    }

    public function handle(): string
    {
        return 'Comment:' . $this->title;
    }
}
