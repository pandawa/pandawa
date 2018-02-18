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

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface RuleRegistryInterface
{
    public function getAllRules(): array;

    public function load(array $rules): void;

    public function has(string $rule): bool;

    public function validate(string $rule, array $data): array;
}
