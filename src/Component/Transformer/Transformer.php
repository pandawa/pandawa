<?php

declare(strict_types=1);

namespace Pandawa\Component\Transformer;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Pandawa\Component\Transformer\Exception\IncludeNotAllowedException;
use Pandawa\Component\Transformer\Exception\SelectNotAllowedException;
use Pandawa\Contracts\Transformer\Context;
use Pandawa\Contracts\Transformer\TransformerInterface;
use RuntimeException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
abstract class Transformer implements TransformerInterface
{
    use ConditionallyTrait;

    /**
     * Available include relations.
     *
     * @var array
     */
    protected array $availableIncludes = [];

    /**
     * Default include relations.
     *
     * @var array
     */
    protected array $defaultIncludes = [];

    /**
     * Available select properties.
     *
     * @var array
     */
    protected array $availableSelects = [];

    /**
     * Default selected properties.
     *
     * @var array
     */
    protected array $defaultSelects = [];

    public function setAvailableIncludes(array $availableIncludes): static
    {
        $this->availableIncludes = $availableIncludes;

        return $this;
    }

    public function setDefaultIncludes(array $defaultIncludes): static
    {
        $this->defaultIncludes = $defaultIncludes;

        return $this;
    }

    public function setAvailableSelects(array $availableSelects): static
    {
        $this->availableSelects = $availableSelects;

        return $this;
    }

    public function setDefaultSelects(array $defaultSelects): static
    {
        $this->defaultSelects = $defaultSelects;

        return $this;
    }


    public function process(Context $context, mixed $data): array
    {
        $transformed = [
            ...$this->processTransform($context, $data),
            ...$this->processIncludes($this->getIncludes($context->includes), $data),
        ];

        if (empty($selects = $this->getSelects($context->selects))) {
            return $transformed;
        }

        return $this->filter($selects, $transformed);
    }

    protected function filter(array $selects, array $data): array
    {
        return Arr::undot(
            Arr::only(
                Arr::dot($data),
                $selects
            )
        );
    }

    protected function processIncludes(array $includes, mixed $data): array
    {
        $included = [];
        foreach ($includes as $include) {
            $method = 'include' . ucfirst(Str::camel($include));

            if (!method_exists($this, $method)) {
                throw new RuntimeException(sprintf(
                    'Missing method "%s" in transformer class "%s".',
                    $method,
                    static::class
                ));
            }

            $included[$include] = $this->{$method}($data);
        }

        return $included;
    }

    protected function processTransform(Context $context, mixed $data): array
    {
        if (!method_exists($this, 'transform')) {
            throw new RuntimeException(sprintf('Class "%s" should has transform method.', static::class));
        }

        $normalized = [];

        foreach ($this->transform($context, $data) as $key => $value) {
            if ($value instanceof MissingValue) {
                continue;
            }

            if ($value instanceof MergeValue) {
                $normalized = [...$normalized, ...$value->data];

                continue;
            }

            $normalized[$key] = $value;
        }

        return $normalized;
    }

    protected function getSelects(array $selects): array
    {
        if (empty($selects)) {
            return $this->defaultSelects;
        }

        if (empty($this->availableSelects)) {
            return $selects;
        }

        return array_filter($selects, function (string $select) {
            if ($this->isAllowed($select, $this->availableSelects)) {
                return true;
            }

            throw new SelectNotAllowedException($select);
        });
    }

    protected function getIncludes(array $includes): array
    {
        if (empty($includes)) {
            return $this->defaultIncludes;
        }

        if (empty($this->availableIncludes)) {
            return $includes;
        }

        return array_filter($includes, function (string $include) {
            if ($this->isAllowed($include, $this->availableIncludes)) {
                return true;
            }

            throw new IncludeNotAllowedException($include);
        });
    }

    protected function isAllowed(string $select, array $stack): bool
    {
        $temp = null;
        do {
            $temp = null === $temp ? $select : substr($temp, 0, strrpos($temp, '.'));

            if (in_array($temp, $stack)) {
                return true;
            }

        } while($temp && str_contains($temp, '.'));

        return false;
    }
}
