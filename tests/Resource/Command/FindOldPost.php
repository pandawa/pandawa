<?php

declare(strict_types=1);

namespace Test\Resource\Command;

use Pandawa\Annotations\Resource\ApiMessage;
use Test\Resource\Model\Post;
use Test\Resource\Transformer\PostTransformer;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
#[ApiMessage('old-post', 'GET', options: [
    'transformer' => [
        'class' => PostTransformer::class,
    ]
])]
class FindOldPost
{
    public function __invoke(): Post
    {
        return new Post(['title' => 'Very Old Post', 'content' => 'very old content']);
    }
}
