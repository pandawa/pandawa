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

namespace Pandawa\Module\Api\Http\Controller;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Pandawa\Component\Ddd\AbstractModel;
use Pandawa\Component\Ddd\Repository\RepositoryInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait InteractsWithRelationsTrait
{
    protected function withRelations($stmt, array $options, string $scope = null): void
    {
        if (null !== $withs = $this->getRelations($options, $scope)) {
            if ($stmt instanceof RepositoryInterface) {
                $stmt->with($withs);
            } else if ($stmt instanceof Builder) {
                $stmt->with($withs);
            } else if ($stmt instanceof AbstractModel) {
                $stmt->load($withs);
            }
        }
    }

    protected function getRelations(array $options, ?string $scope): ?array
    {
        if (null !== $scope) {
            $key = sprintf('withs.%s', $scope);
            $withs = array_get($options, $key, array_get($options, 'withs'));
            $withs = $withs ? array_except($withs, ['index', 'show', 'destroy', 'store', 'update']) : null;
        } else {
            $withs = array_get($options, 'withs');
        }

        if (null !== $withs) {
            return array_map(
                function (string $rel) {
                    return Str::camel($rel);
                },
                $withs
            );
        }

        return null;
    }
}
