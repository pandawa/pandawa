<?php

declare(strict_types=1);

namespace Test\Transformer;

use Pandawa\Component\Transformer\Transformer;
use Pandawa\Contracts\Transformer\Context;
use Test\Transformer\Model\Post;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class PostTransformer extends Transformer
{
    protected array $availableSelects = [
        'title',
        'content',
        'author.name',
        'published',
        'promote',
    ];

    protected array $defaultSelects = [
        'title',
        'content',
        'published',
        'promote',
    ];

    protected function transform(Context $context, Post $post): array
    {
        return [
            'title' => $post->title,
            'content' => $this->when(2 == $context->version, $post->content),
            'author' => $post->author,
            $this->mergeWhen(2 === $context->version, fn() => [
                'published' => true,
                'promote' => false,
            ])
        ];
    }
}
