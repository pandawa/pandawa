<?php

declare(strict_types=1);

namespace Test\Transformer;

use Pandawa\Component\Transformer\CollectionTransformer;
use Pandawa\Component\Transformer\EloquentTransformer;
use Pandawa\Component\Transformer\Exception\SelectNotAllowedException;
use Pandawa\Contracts\Transformer\Context;
use PHPUnit\Framework\TestCase;
use Test\Transformer\Model\Post;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class TransformerTest extends TestCase
{
    public function testEloquentTransform(): void
    {
        $transformer = new EloquentTransformer();
        $post = new Post([
            'title' => 'New Post',
            'content' => 'Hello world',
            'author' => [
                'name' => 'Iqbal',
                'rate' => 5,
            ]
        ]);

        $this->assertSame(['title' => 'New Post', 'content' => 'Hello world', 'author' => ['name' => 'Iqbal', 'rate' => 5]], $transformer->process(new Context(), $post));
        $this->assertSame(['title' => 'New Post', 'author' => ['name' => 'Iqbal']], $transformer->process(
            new Context(selects: ['title', 'author.name']),
            $post
        ));
    }

    public function testCollectionTransformer(): void
    {
        $collectionTransformer = new CollectionTransformer(new EloquentTransformer());
        $collection = collect([
            new Post([
                'title' => 'New Post',
                'content' => 'Hello world',
                'author' => [
                    'name' => 'Iqbal',
                    'rate' => 5,
                ]
            ]),
            new Post([
                'title' => 'Old Post',
                'content' => 'Hello World',
                'author' => [
                    'name' => 'Adi',
                    'rate' => 3,
                ]
            ]),
        ]);

        $this->assertSame(
            [
                ['title' => 'New Post', 'author' => ['name' => 'Iqbal']],
                ['title' => 'Old Post', 'author' => ['name' => 'Adi']],
            ],
            $collectionTransformer->process(
                new Context(selects: ['title', 'author.name']),
                $collection
            )
        );
    }

    public function testCustomTransformer(): void
    {
        $transformer = new PostTransformer();
        $post = new Post([
            'title' => 'New Post',
            'content' => 'Hello world',
            'author' => [
                'name' => 'Iqbal',
                'rate' => 5,
            ]
        ]);

        $this->assertSame(['title' => 'New Post'], $transformer->process(new Context(), $post));
        $this->assertSame(['title' => 'New Post'], $transformer->process(new Context(selects: ['title', 'content']), $post));
        $this->assertSame(
            ['title' => 'New Post', 'author' => ['name' => 'Iqbal']],
            $transformer->process(new Context(selects: ['title', 'author.name']), $post)
        );

        $this->assertSame(
            [
                'title' => 'New Post',
                'content' => 'Hello world',
                'published' => true,
                'promote' => false,
            ],
            $transformer->process(new Context(version: 2), $post)
        );

        $this->assertSame(['title' => 'New Post', 'content' => 'Hello world'], $transformer->process(new Context(selects: ['title', 'content'], version: 2), $post));

        $this->expectException(SelectNotAllowedException::class);
        $this->assertSame(['title' => 'New Post'], $transformer->process(new Context(selects: ['author']), $post));
        $this->assertSame(['title' => 'New Post'], $transformer->process(new Context(selects: ['author.rate']), $post));

    }
}
