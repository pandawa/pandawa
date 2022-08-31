<?php

declare(strict_types=1);

namespace Test\Resource\Command;

use Test\Resource\Model\Post;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class CreatePost
{
    public function __construct(
        public readonly string $title,
        public readonly string $content,
    ) {
    }

    public function __invoke(): Post
    {
        return new Post(['title' => $this->title, 'content' => $this->content]);
    }
}
