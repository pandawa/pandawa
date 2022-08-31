<?php

declare(strict_types=1);

namespace Test\Resource;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Pandawa\Component\Resource\Formatter\CsvFormatter;
use Pandawa\Component\Resource\Formatter\FormatterResolver;
use Pandawa\Component\Resource\Formatter\JsonFormatter;
use Pandawa\Component\Resource\Formatter\XmlFormatter;
use Pandawa\Component\Resource\Formatter\YamlFormatter;
use Pandawa\Component\Resource\Middleware\AddVersionMiddleware;
use Pandawa\Component\Resource\Renderer;
use Pandawa\Component\Transformer\EloquentTransformer;
use Pandawa\Contracts\Resource\Formatter\FormatterResolverInterface;
use Pandawa\Contracts\Resource\RendererInterface;
use Pandawa\Contracts\Transformer\Context;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Test\Resource\Model\Post;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class RendererTest extends TestCase
{
    /**
     * @dataProvider provideTests
     */
    public function testRender(array $config, array $test): void
    {
        $renderer = $this->createRenderer();

        $renderer->setDefaultWrapper($config['wrapper']);

        $response = $renderer->render($config['context'], $config['resource'], $config['transformer']);

        $this->assertSame($test['result'], $response->getContent());
        $this->assertSame($test['http_code'], $response->getStatusCode());
        $this->assertSame($test['content_type'], $response->headers->get('Content-Type'));
    }

    public function provideTests(): array
    {
        return [
            'Test Single Resource Without Wrapper' => [
                [
                    'resource'    => new Post(['title' => 'New Post', 'content' => 'content here']),
                    'transformer' => new EloquentTransformer(),
                    'wrapper'     => null,
                    'context'     => new Context(
                        selects: ['title'],
                        version: 'v1',
                        options: ['http_code' => 200],
                        request: tap(Request::create('', 'GET'), function (Request $request) {
                            $request->headers->set('Accept', 'application/json');
                        })
                    ),
                ],
                [
                    'result' => json_encode([
                        'title' => 'New Post',
                        'meta' => [
                            'version' => 'v1',
                        ]
                    ]),
                    'http_code' => 200,
                    'content_type' => 'application/json',
                ]
            ],
            'Test Single Resource With Wrapper' => [
                [
                    'resource'    => new Post(['title' => 'New Post', 'content' => 'content here']),
                    'transformer' => new EloquentTransformer(),
                    'wrapper'     => 'data',
                    'context'     => new Context(
                        version: 'v1',
                        options: ['http_code' => 200],
                        request: tap(Request::create('', 'GET'), function (Request $request) {
                            $request->headers->set('Accept', 'application/json');
                        })
                    ),
                ],
                [
                    'result' => json_encode([
                        'data' => [
                            'title' => 'New Post',
                            'content' => 'content here',
                        ],
                        'meta' => [
                            'version' => 'v1',
                        ]
                    ]),
                    'http_code' => 200,
                    'content_type' => 'application/json',
                ]
            ],
            'Test Collection Resource' => [
                [
                    'resource'    => new Collection([new Post(['title' => 'New Post', 'content' => 'content here'])]),
                    'transformer' => new EloquentTransformer(),
                    'wrapper'     => 'data',
                    'context'     => new Context(
                        selects: ['title'],
                        version: 'v1',
                        options: ['http_code' => 201],
                        request: tap(Request::create('', 'GET'), function (Request $request) {
                            $request->headers->set('Accept', 'application/json');
                        })
                    ),
                ],
                [
                    'result' => json_encode([
                        'data' => [
                            [
                                'title' => 'New Post',
                            ]
                        ],
                        'meta' => [
                            'version' => 'v1',
                        ]
                    ]),
                    'http_code' => 201,
                    'content_type' => 'application/json',
                ]
            ],
            'Test Pagination Collection Resource' => [
                [
                    'resource'    => new LengthAwarePaginator(
                        [new Post(['title' => 'New Post', 'content' => 'content here'])],
                        1,
                        1,
                        1
                    ),
                    'transformer' => new EloquentTransformer(),
                    'wrapper'     => 'data',
                    'context'     => new Context(
                        version: 'v1',
                        options: ['http_code' => 200],
                        request: tap(Request::create(''), function (Request $request) {
                            $request->headers->set('Accept', 'application/json');
                        })
                    ),
                ],
                [
                    'result' => json_encode([
                        'meta' => [
                            'current_page' => 1,
                            'from' => 1,
                            'last_page' => 1,
                            'path' => '/',
                            'per_page' => 1,
                            'to' => 1,
                            'total' => 1,
                            'version' => 'v1',
                        ],
                        'links' => [
                            'first' => '/?page=1',
                            'last' => '/?page=1',
                            'prev' => null,
                            'next' => null,
                        ],
                        'data' => [
                            [
                                'title' => 'New Post',
                                'content' => 'content here'
                            ]
                        ],
                    ]),
                    'http_code' => 200,
                    'content_type' => 'application/json',
                ]
            ],
        ];
    }

    protected function createRenderer(): RendererInterface
    {
        return new Renderer(
            new Container(),
            $this->createFormatterResolver(),
            $this->createSerializer(),
            'application/json',
            middlewares: [
                AddVersionMiddleware::class,
            ]
        );
    }

    protected function createFormatterResolver(): FormatterResolverInterface
    {
        return new FormatterResolver([
            new CsvFormatter(),
            new JsonFormatter(),
            new XmlFormatter(),
            new YamlFormatter(),
        ]);
    }

    protected function createSerializer(): SerializerInterface
    {
        return new Serializer(
            [new ObjectNormalizer()],
            [new JsonEncoder()],
        );
    }
}
