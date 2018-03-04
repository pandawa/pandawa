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

    public function transform($data, array $tags = [])
    {
        /** @var TransformerInterface $transformer */
        foreach (array_reverse($this->transformers) as $transformer) {
            if ($transformer->support($data, $tags)) {
                $data = $transformer->transform($data, $tags);

                if (is_array($data) || $data instanceof Collection) {
                    foreach ($data as $key => $value) {
                        if (!is_scalar($value)) {
                            $data[$key] = $this->transform($value, $tags);
                        }
                    }
                }
            }
        }

        return $data;
    }
}
