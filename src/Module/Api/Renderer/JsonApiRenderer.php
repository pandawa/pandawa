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

namespace Pandawa\Module\Api\Renderer;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Pandawa\Component\Transformer\TransformerRegistryInterface;
use Pandawa\Module\Api\Http\Resource\JsonResource;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class JsonApiRenderer implements RendererInterface
{
    /**
     * @var TransformerRegistryInterface
     */
    private $transformerRegistry;

    /**
     * Constructor.
     *
     * @param TransformerRegistryInterface $transformerRegistry
     */
    public function __construct(TransformerRegistryInterface $transformerRegistry)
    {
        $this->transformerRegistry = $transformerRegistry;
    }

    public function render(Request $request, $data, array $options = []): Responsable
    {
        return new JsonResource($data, $this->transformerRegistry, $options);
    }
}
