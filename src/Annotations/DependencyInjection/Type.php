<?php

declare(strict_types=1);

namespace Pandawa\Annotations\DependencyInjection;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
enum Type
{
    case SERVICE;
    case CONFIG;
    case VALUE;
    case TAG;
}
