<?php

declare(strict_types=1);

namespace Test\Event;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class PostCreatedListener
{
    public function handle(PostCreated $event): string
    {
        return 'Post Created: ' . $event->title;
    }
}
