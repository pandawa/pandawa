<?php

declare(strict_types=1);

namespace Pandawa\Component\Foundation\Bootstrap;

use Pandawa\Component\Foundation\Application;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ConfigureBundles
{
    public function bootstrap($app): void
    {
        if ($app instanceof Application) {
            $app->configure();
        }
    }
}
