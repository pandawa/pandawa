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

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Pandawa\Component\Ddd\AbstractModel;
use Pandawa\Module\Api\Transformer\Transformer;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class InvokableController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function handle(Request $request)
    {
        $route = $request->route();

        $data = $data = array_merge($request->all(), $request->route()->parameters());
        $message = $this->getMessage($request);
        $result = $this->dispatch(new $message($data));

        $this->withRelations($result, $route->defaults);

        return new Transformer($result);
    }

    private function getMessage(Request $request): string
    {
        if (null !== $message = array_get($request->route()->defaults, 'message')) {
            return $message;
        }

        throw new InvalidArgumentException('Parameter "message" not found on route.');
    }

    private function withRelations($stmt, array $options): void
    {
        if (null !== $withs = array_get($options, 'withs')) {
            $withs = array_map(
                function (string $rel) {
                    return Str::camel($rel);
                },
                $withs
            );

            if ($stmt instanceof Builder) {
                $stmt->with($withs);
            } else if ($stmt instanceof AbstractModel) {
                $stmt->load($withs);
            }
        }
    }
}
