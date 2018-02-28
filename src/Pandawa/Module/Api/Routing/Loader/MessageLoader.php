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
use Pandawa\Component\Message\MessageRegistryInterface;
use Pandawa\Module\Api\Http\Controller\InvokableControllerInterface;
use Route;
use RuntimeException;

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
     * @var null|MessageRegistryInterface
     */
    private $messageRegistry;

    /**
     * Constructor.
     *
     * @param string                        $invokableController
     * @param MessageRegistryInterface|null $messageRegistry
     */
    public function __construct(string $invokableController, MessageRegistryInterface $messageRegistry = null)
    {
        if (!in_array(InvokableControllerInterface::class, class_implements($invokableController))) {
            throw new RuntimeException(
                sprintf(
                    'Controller "%s" should implement "%s"',
                    $invokableController,
                    InvokableControllerInterface::class
                )
            );
        }

        $this->invokableController = $invokableController;
        $this->messageRegistry = $messageRegistry;
    }

    /**
     * {@inheritdoc}
     */
    protected function createRoutes(string $type, string $path, string $controller, array $route): array
    {
        if (null === $message = array_get($route, 'message')) {
            throw new InvalidArgumentException('Route with type "message" should has message class.');
        }

        if (null === $methods = array_get($route, 'methods')) {
            throw new InvalidArgumentException('Missing "methods" parameter on type route type "message".');
        }

        if (null === $this->messageRegistry) {
            throw new RuntimeException('There are not message registry detected.');
        }

        if (!$this->messageRegistry->has($message)) {
            throw new RuntimeException(sprintf('Message "%s" is not registered.', $message));
        }

        return [Route::match($methods, $path, sprintf('%s@handle', $controller))];
    }

    protected function getRouteDefaultParameters(array $route): array
    {
        return ['message' => array_get($route, 'message')];
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
}
