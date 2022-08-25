<?php

declare(strict_types=1);

namespace Test\Bus\Handler;

use Test\Bus\Command\CreatePost;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class CreatePostHandler
{
    public function handle(CreatePost $post): string
    {
        return 'Title:' . $post->title;
    }
}
