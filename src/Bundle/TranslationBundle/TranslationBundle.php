<?php

declare(strict_types=1);

namespace Pandawa\Bundle\TranslationBundle;

use Illuminate\Translation\TranslationServiceProvider;
use Pandawa\Bundle\FoundationBundle\Plugin\RegisterBundlesPlugin;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Contracts\Foundation\HasPluginInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class TranslationBundle extends Bundle implements HasPluginInterface
{
    public function plugins(): array
    {
        return [
            new RegisterBundlesPlugin([TranslationServiceProvider::class]),
        ];
    }
}
