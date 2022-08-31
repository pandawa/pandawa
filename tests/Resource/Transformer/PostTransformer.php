<?php

declare(strict_types=1);

namespace Test\Resource\Transformer;

use Pandawa\Component\Transformer\Transformer;
use Pandawa\Contracts\Transformer\Context;
use Test\Resource\Model\Post;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class PostTransformer extends Transformer
{
    protected ?string $wrapper = 'post';

    protected array $defaultSelects = [
        'title',
    ];

    protected function transform(Context $context, Post $post): array
    {
        return [
            'title' => $post->title,
            'content' => $post->content,
        ];
    }
}
