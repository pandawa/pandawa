<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Foundation;

use Illuminate\Contracts\Foundation\Application;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface ApplicationInterface extends Application
{
    public function getFoundationConfig(?string $key, mixed $default = null): mixed;
}
