<?php
declare(strict_types=1);

namespace Pandawa\Component\Presenter;

use Pandawa\Component\Support\NameableClassTrait;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait NameablePresenterTrait
{
    use NameableClassTrait {
        NameableClassTrait::name as baseName;
    }

    public static function name(): string
    {
        $name = static::baseName();

        return preg_replace('/-(presenter)$/', '', $name);
    }
}