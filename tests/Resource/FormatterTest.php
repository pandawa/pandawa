<?php

declare(strict_types=1);

namespace Test\Resource;

use Illuminate\Http\Request;
use Pandawa\Component\Resource\Formatter\CsvFormatter;
use Pandawa\Component\Resource\Formatter\FormatterResolver;
use Pandawa\Component\Resource\Formatter\JsonFormatter;
use Pandawa\Component\Resource\Formatter\XmlFormatter;
use Pandawa\Component\Resource\Formatter\YamlFormatter;
use Pandawa\Contracts\Resource\Formatter\FormatterResolverInterface;
use Pandawa\Contracts\Transformer\Context;
use PHPUnit\Framework\TestCase;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class FormatterTest extends TestCase
{
    /**
     * @dataProvider provideMimeTypes
     */
    public function testFormat(array $acceptMimes, string $contentType, string $format): void
    {
        $request = new Request();
        $request->headers->add([
            'Accept' => implode(', ', $acceptMimes)
        ]);

        $resolver = $this->resolver()->resolve($request);

        $this->assertNotNull($resolver);
        $this->assertSame($contentType, $resolver->getContentType());
        $this->assertSame($format, $resolver->getFormat());

        $response = $resolver->toResponse(
            new Context(options: ['http_code' => 200]),
            'Hello world'
        );

        $this->assertSame($contentType, $response->headers->get('Content-Type'));
    }

    public function provideMimeTypes(): array
    {
        return [
            'Test Csv' => [
                ['text/csv', 'application/csv'],
                'text/csv',
                'csv',
            ],
            'Test Json' => [
                ['application/json'],
                'application/json',
                'json',
            ],
            'Test Xml' => [
                ['application/xhtml+xml', 'application/xml', 'text/xml'],
                'application/xml',
                'xml',
            ],
            'Test Yaml' => [
                ['text/yaml', 'application/x-yaml'],
                'text/yaml',
                'yaml',
            ]
        ];
    }

    protected function resolver(): FormatterResolverInterface
    {
        return new FormatterResolver([
            new CsvFormatter(),
            new JsonFormatter(),
            new XmlFormatter(),
            new YamlFormatter(),
        ]);
    }
}
