<?php
declare(strict_types=1);

namespace Pandawa\Module\Api\Http\Controller;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Pandawa\Component\Presenter\PresenterInterface;
use Pandawa\Component\Presenter\PresenterRegistryInterface;
use InvalidArgumentException;
use RuntimeException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class PresenterHandler implements PresenterHandlerInterface
{
    /**
     * @var PresenterRegistryInterface
     */
    private $registry;

    /**
     * Constructor.
     *
     * @param PresenterRegistryInterface $registry
     */
    public function __construct(PresenterRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param Request $request
     *
     * @return Responsable|View|PresenterInterface
     */
    public function __invoke(Request $request)
    {
        if (null !== $presenter = array_get($request->route()->defaults, 'presenter')) {
            if (null !== $this->presenterRegistry()) {
                return $this->presenterRegistry()->get($presenter)->render($request);
            }

            throw new RuntimeException('Presenter registry not registered.');
        }

        throw new InvalidArgumentException('Parameter "presenter" not found on route.');
    }

    /**
     * @return PresenterRegistryInterface|null
     */
    private function presenterRegistry(): ?PresenterRegistryInterface
    {
        if (app()->has(PresenterRegistryInterface::class)) {
            return app(PresenterRegistryInterface::class);
        }

        return null;
    }
}