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

namespace Pandawa\Component\Ddd;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class LockModes
{
    const PESSIMISTIC_WRITE = 1;
    const PESSIMISTIC_READ = 2;
}
