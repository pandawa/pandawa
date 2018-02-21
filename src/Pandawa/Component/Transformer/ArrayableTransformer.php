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

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ArrayableTransformer implements TransformerInterface
{
    public function transform(Request $request, $data)
    {
        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        return $data;
    }

    public function support(Request $request, $data): bool
    {
        return is_array($data) || $data instanceof Arrayable;
    }
}
