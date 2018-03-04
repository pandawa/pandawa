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

namespace Pandawa\Component\Ddd\Specification;

use Illuminate\Support\Str;
use ReflectionClass;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait NameableSpecificationTrait
{
    public static function name(): string
    {
        $class = new ReflectionClass(get_called_class());

        $className = preg_replace('/Specification$/', '', $class->getShortName());

        return Str::kebab($className);
    }
}
