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

    public function transform($data, array $tags = [], array $context = [])
    {
        if (is_object($data) && $this->isCircularReference($data, $context)) {
            return null;
        }

        if (null === $data || is_scalar($data)) {
            return $data;
        }

        if (is_array($data) || $data instanceof Collection) {
            foreach ($data as $key => $datum) {
                $data[$key] = $this->transform($datum, $tags, $context);
            }

            return $data instanceof Collection ? $data->all() : $data;
        }

        /** @var TransformerInterface $transformer */
        foreach (array_reverse($this->transformers) as $transformer) {
            if ($transformer->support($data, $tags)) {
                $data = $transformer->transform($data, $tags);
            }
        }

        return $this->transform($data, $tags, $context);
    }

    private function isCircularReference($data, &$context)
    {
        $objectHash = spl_object_hash($data);

        if (isset($context['circular_reference_limit'][$objectHash])) {
            if ($context['circular_reference_limit'][$objectHash] >= 1) {
                unset($context['circular_reference_limit'][$objectHash]);

                return true;
            }

            ++$context['circular_reference_limit'][$objectHash];
        } else {
            $context['circular_reference_limit'][$objectHash] = 1;
        }

        return false;
    }
}
