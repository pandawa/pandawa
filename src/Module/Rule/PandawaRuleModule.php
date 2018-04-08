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

namespace Pandawa\Module\Rule;

use Illuminate\Validation\Factory;
use Pandawa\Component\Module\AbstractModule;
use Pandawa\Component\Module\Provider\LanguageProviderTrait;
use Pandawa\Module\Rule\Validator\Validator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class PandawaRuleModule extends AbstractModule
{
    use LanguageProviderTrait;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function build(): void
    {
        $this->validatorFactory()->resolver(
            function ($translator, $data, $rules, $messages, $customAttributes) {
                return new Validator($translator, $data, $rules, $messages, $customAttributes);
            }
        );
    }

    /**
     * @return Factory
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function validatorFactory(): Factory
    {
        return $this->app->get(Factory::class);
    }
}
