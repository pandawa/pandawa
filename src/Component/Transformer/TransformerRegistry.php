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

use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class TransformerRegistry implements TransformerRegistryInterface
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var TransformerInterface[]|array[]
     */
    private $transformers;

    /**
     * Constructor.
     *
     * @param Application $app
     * @param array       $transformers
     */
    public function __construct(Application $app, array $transformers = [])
    {
        $this->app = $app;
        $this->transformers = $transformers;
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

        foreach (array_reverse($this->transformers) as $key => $transformer) {
            if (is_string($transformer)) {
                $transformer = $this->transformers[$key] = $this->getTransformer($transformer);
            }

            if ($transformer->support($data, $tags)) {
                $data = $transformer->transform($data, $tags);
            }
        }

        return $this->transform($data, $tags, $context);
    }

    /**
     * @param string $transformer
     *
     * @return TransformerInterface
     */
    private function getTransformer(string $transformer): TransformerInterface
    {
        if ($this->app[$transformer]) {
            return $this->app[$transformer];
        }

        return $this->app->make($transformer);
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
