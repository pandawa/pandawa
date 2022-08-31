<?php

declare(strict_types=1);

namespace Test\Resource\Command;

use Test\Resource\Model\Post;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class FindPost
{
    public function __construct()
    {
    }

    public function __invoke(): Post
    {
        return new Post(['title' => 'Old Post', 'content' => 'old content']);
    }
}
