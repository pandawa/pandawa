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

namespace Pandawa\Module\Api\Routing\Loader;

use InvalidArgumentException;
use Route;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class MessageLoader extends AbstractLoader
{
    /**
     * @var string
     */
    private $invokableController;

    /**
     * Constructor.
     *
     * @param string $invokableController
     */
    public function __construct(string $invokableController)
    {
        $this->invokableController = $invokableController;
    }

    /**
     * {@inheritdoc}
     */
    protected function createRoute(string $type, string $path, string $controller, array $options, array $route)
    {
        if (null === array_get($route, 'message')) {
            throw new InvalidArgumentException('Route with type "message" should has message class.');
        }

        $options['defaults']['message'] = array_get($route, 'message');

        $route = Route::{$type}($path, sprintf('%s@handle', $controller), $options);
        $route->defaults = array_merge((array) $route->defaults, $options['defaults']);

        return $route;
    }

    /**
     * {@inheritdoc}
     */
    public function support(string $type): bool
    {
        return 'message' === $type;
    }

    /**
     * {@inheritdoc}
     */
    protected function getController(array $route): string
    {
        return $this->invokableController;
    }

    /**
     * {@inheritdoc}
     */
    protected function getType(array $route): string
    {
        if (null === array_get($route, 'method')) {
            throw new InvalidArgumentException('Missing "method" parameter on type route type "message".');
        }

        return array_get($route, 'method');
    }
}
