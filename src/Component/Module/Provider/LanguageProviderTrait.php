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

namespace Pandawa\Component\Module\Provider;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait LanguageProviderTrait
{
    /**
     * @var string
     */
    protected $langPath = 'Resources/lang';

    public function bootLanguageProvider(): void
    {
        $this->loadTranslationsFrom($this->getCurrentPath() . '/' . $this->langPath, $this->getModuleName());
        $this->loadJsonTranslationsFrom($this->getCurrentPath() . '/' . $this->langPath);
    }
}
