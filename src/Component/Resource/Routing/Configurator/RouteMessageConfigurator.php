<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource\Routing\Configurator;

use Illuminate\Routing\Route;
use InvalidArgumentException;
use Pandawa\Contracts\Bus\Message\RegistryInterface;
use Pandawa\Contracts\Routing\RouteConfiguratorInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class RouteMessageConfigurator implements RouteConfiguratorInterface
{
    public function __construct(protected readonly RegistryInterface $registry)
    {
    }

    public function configure(Route $route, array $options): Route
    {
        if ($message = $options['message'] ?? null) {
            $this->validateMessage($message);

            $route->defaults = [
                ...($route->defaults ?? []),
                'message' => $message,
            ];
        }

        return $route;
    }

    protected function validateMessage(string $message): void
    {
        if (!$this->registry->hasName($message) && !$this->registry->has($message) && !class_exists($message)) {
            throw new InvalidArgumentException(
                sprintf('Message "%s" is not supported.', $message)
            );
        }
    }
}
