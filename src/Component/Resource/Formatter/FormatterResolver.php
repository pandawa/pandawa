<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource\Formatter;

use Illuminate\Http\Request;
use Pandawa\Contracts\Resource\Formatter\FormatterInterface;
use Pandawa\Contracts\Resource\Formatter\FormatterResolverInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class FormatterResolver implements FormatterResolverInterface
{
    /**
     * @var FormatterInterface[]
     */
    protected array $formatters = [];

    public function __construct(iterable $formatters = [])
    {
        foreach ($formatters as $formatter) {
            $this->add($formatter);
        }
    }

    public function add(FormatterInterface $formatter): void
    {
        $this->formatters[] = $formatter;
    }

    public function resolve(Request $request): ?FormatterInterface
    {
        foreach ($this->formatters as $formatter) {
            if ($formatter->supports($request)) {
                return $formatter;
            }
        }

        return null;
    }
}
