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

namespace Pandawa\Component\Validation;

use Illuminate\Http\Request;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait RequestValidationTrait
{
    public function validateRequest(Request $request): array
    {
        $route = $request->route();
        $data = array_merge(
            $request->route()->parameters(),
            $request->all(),
            $request->files->all()
        );

        if (!empty($rules = (array) array_get($route->defaults, 'rules')) && null !== $this->ruleRegistry()) {
            $filtered = [];

            foreach ($rules as $rule) {
                $filtered = array_merge($filtered, $this->ruleRegistry()->validate($rule, $data));
            }

            return $filtered;
        }

        return $data;
    }

    private function ruleRegistry(): ?RuleRegistryInterface
    {
        if (app()->has(RuleRegistryInterface::class)) {
            return app(RuleRegistryInterface::class);
        }

        return null;
    }
}
