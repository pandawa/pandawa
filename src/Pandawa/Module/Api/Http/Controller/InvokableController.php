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

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use InvalidArgumentException;
use Pandawa\Component\Message\AbstractQuery;
use Pandawa\Component\Message\MessageRegistryInterface;
use Pandawa\Component\Validation\RequestValidationTrait;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class InvokableController extends Controller implements InvokableControllerInterface
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, InteractsWithRelationsTrait, InteractsWithTransformerTrait;
    use RequestValidationTrait;

    public function handle(Request $request)
    {
        $route = $request->route();

        $data = array_merge(
            $this->validateRequest($request),
            ['auth_user' => $request->getUser()]
        );

        $message = $this->getMessage($request);
        $message = new $message($data);

        if ($message instanceof AbstractQuery) {
            $this->modifyQuery($message, $request);
        }

        $result = $this->dispatch($message);

        $this->withRelations($result, $route->defaults);

        return $this->transform($result);
    }

    private function getMessage(Request $request): string
    {
        if (null !== $message = array_get($request->route()->defaults, 'message')) {
            if (null !== $this->messageRegistry()) {
                return $this->messageRegistry()->get($message)->getMessageClass();
            }

            return $message;
        }

        throw new InvalidArgumentException('Parameter "message" not found on route.');
    }

    private function modifyQuery(AbstractQuery $query, Request $request): void
    {
        $route = $request->route();

        if (null !== $withs = array_get($route->defaults, 'withs')) {
            $query->withRelations($withs);
        }

        if (true === array_get($route->defaults, 'paginate', false)) {
            $query->paginate($request->get('limit', 50));
        }
    }

    private function messageRegistry(): ?MessageRegistryInterface
    {
        if (app()->has(MessageRegistryInterface::class)) {
            return app(MessageRegistryInterface::class);
        }

        return null;
    }
}
