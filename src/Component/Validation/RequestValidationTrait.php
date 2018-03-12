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
use Illuminate\Routing\Route;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait RequestValidationTrait
{
    public function validateRequest(Request $request, string $action = null): array
    {
        $route = $request->route();
        $data = array_merge(
            (array) $route->parameter('defaults', []),
            $request->all(),
            $request->files->all(),
            $this->getRouteParameters($route)
        );

        if (!empty($rules = $this->getAllowedRules($route, $action))) {
            $filtered = [];

            foreach ($rules as $rule) {
                $filtered = array_merge($filtered, $this->ruleRegistry()->validate($rule, $data));
            }

            return $filtered;
        }

        return $data;
    }

    protected function getAllData(Request $request): array
    {
        $route = $request->route();

        return array_merge(
            (array) $route->parameter('defaults', []),
            $request->all(),
            $request->files->all(),
            $this->getRouteParameters($route)
        );
    }

    private function ruleRegistry(): ?RuleRegistryInterface
    {
        if (app()->has(RuleRegistryInterface::class)) {
            return app(RuleRegistryInterface::class);
        }

        return null;
    }

    private function getRouteParameters(Route $route): array
    {
        return array_merge(
            array_except(
                $route->parameters(),
                ['type', 'middleware', 'resource', 'message', 'rules', 'defaults', 'criteria', 'parameters']
            ),
            $route->parameter('parameters', [])
        );
    }

    private function getAllowedRules(Route $route, ?string $action): array
    {
        if (!empty($rules = (array) array_get($route->defaults, 'rules')) && null !== $this->ruleRegistry()) {
            if (null !== $action) {
                $allowed = [];

                foreach ($rules as $rule) {
                    if (is_array($rule) && null !== $ruleName = array_get($rule, 'name')) {
                        if (!empty($only = (array) array_get($rule, 'only'))) {
                            if (in_array($action, $only, true)) {
                                $allowed[] = $ruleName;
                            }
                        } else {
                            if (!empty($except = (array) array_get($rule, 'except'))) {
                                if (!in_array($action, $except, true)) {
                                    $allowed[] = $ruleName;
                                }
                            } else {
                                $allowed[] = $ruleName;
                            }
                        }

                        continue;
                    }

                    $allowed[] = $rule;
                }

                return $allowed;
            }
        }

        return $rules;
    }
}
