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

namespace Pandawa\Module\Api\Http\Controller;

use Illuminate\Http\Request;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface InvokableControllerInterface
{
    public function handle(Request $request);
}
