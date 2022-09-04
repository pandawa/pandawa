<?php

declare(strict_types=1);

namespace Test\Serializer;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class Post
{
    public function __construct(public readonly string $title, public readonly string $content)
    {
    }
}
