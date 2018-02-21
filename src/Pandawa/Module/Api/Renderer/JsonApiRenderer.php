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
use Pandawa\Component\Transformer\TransformerInterface;
use Pandawa\Module\Api\Http\Resource\JsonResource;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class JsonApiRenderer implements RendererInterface
{
    /**
     * @var TransformerInterface
     */
    private $transformer;

    /**
     * Constructor.
     *
     * @param TransformerInterface $transformer
     */
    public function __construct(TransformerInterface $transformer)
    {
        $this->transformer = $transformer;
    }

    public function render(Request $request, $data): Responsable
    {
        return new JsonResource($data, $this->transformer);
    }
}
