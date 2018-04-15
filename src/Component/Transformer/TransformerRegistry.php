<?php
/**
 * This file is part of the Pandawa package.
 *
 * (c) 2018 Pandawa <https://github.com/bl4ckbon3/pandawa>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Pandawa\Component\Transformer;

use Illuminate\Support\Collection;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class TransformerRegistry implements TransformerRegistryInterface
{
    /**
     * @var TransformerInterface[]
     */
    private $transformers = [];

    /**
     * @var array
     */
    private $transformed = [];

    /**
     * @var string
     */
    private $trx;

    /**
     * Constructor.
     *
     * @param TransformerInterface[] $transformers
     */
    public function __construct(array $transformers = [])
    {
        foreach ($transformers as $transformer) {
            $this->add($transformer);
        }
    }

    public function add(TransformerInterface $transformer): void
    {
        $this->transformers[] = $transformer;
    }

    public function transform($data, array $tags = [], object $parent = null)
    {
        $trx = $this->begin();
        $transformed = $data;

        if (is_array($data) || $data instanceof Collection) {
            $transformed = [];
            foreach ($data as $key => $value) {
                $transformed[$key] = $this->transform($value, $tags, $parent);
            }
        } else if (!is_scalar($data)) {
            /** @var TransformerInterface $transformer */
            foreach (array_reverse($this->transformers) as $transformer) {
                if ($transformer->support($transformed, $tags)) {
                    $transformed = $this->transformData($transformer, $transformed, $tags, $parent);
                }
            }
        }

        $this->end($trx);

        return $transformed;
    }

    private function transformData(TransformerInterface $transformer, $data, array $tags, object $parent = null)
    {
        if (is_object($data) && $parent) {
            if ($this->isCircularDependency($data, $parent)) {
                if ($transformer instanceof HandleCircularDependencyInterface) {
                    return $transformer->handleCircular($data, $tags);
                }

                return null;
            }
        }

        $transformed = $transformer->transform($data, $tags, $this);

        if (is_array($data) || $data instanceof Collection) {
            $transformed = $this->transform($transformed, $tags, $this);
        }

        return $transformed;
    }

    private function isCircularDependency($data, object $parent): bool
    {
        if (is_array($data) || $data instanceof Collection) {
            return false;
        }

        $parentHash = spl_object_hash($parent);
        $dataHash = spl_object_hash($data);

        if (isset($this->transformed[$parentHash][$dataHash])) {
            return true;
        }

        $this->transformed[$parentHash][$dataHash] = true;

        return false;
    }

    /**
     * Begin transaction.
     *
     * @return string
     */
    private function begin(): string
    {
        $trx = uniqid();

        if (null === $this->trx) {
            $this->trx = $trx;
        }

        return $trx;
    }

    /**
     * End transaction.
     *
     * @param string $trx
     */
    private function end(string $trx): void
    {
        if ($trx === $this->trx) {
            $this->trx = null;
            $this->transformed = [];
        }
    }
}
