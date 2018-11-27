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

namespace Pandawa\Component\Message;

use InvalidArgumentException;
use RuntimeException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class MessageRegistry implements MessageRegistryInterface
{
    /**
     * @var Metadata[]
     */
    private $messages = [];

    /**
     * Constructor.
     *
     * @param array $messages
     */
    public function __construct(array $messages = [])
    {
        $this->messages = $messages;
    }

    public function add(string $message, Metadata $metadata): void
    {
        if ($this->has($message)) {
            throw new RuntimeException(sprintf('Message "%s" already registered.', $message));
        }

        $this->messages[$message] = $metadata;
    }

    public function has(string $message): bool
    {
        return array_key_exists($message, $this->messages);
    }

    public function remove(string $message): void
    {
        $this->assertExists($message);

        unset($this->messages[$message]);
    }

    public function get(string $message): Metadata
    {
        $this->assertExists($message);

        $metadata = $this->messages[$message];

        if ($metadata instanceof Metadata) {
            return $metadata;
        }

        if (!array_key_exists('messageClass', $metadata) || !array_key_exists('handlerClass', $metadata)) {
            throw new InvalidArgumentException(sprintf('Invalid metadata for message "%s"', $message));
        }

        return $this->messages[$message] = new Metadata($metadata['messageClass'], $metadata['handlerClass']);
    }

    private function assertExists(string $message): void
    {
        if (!$this->has($message)) {
            throw new RuntimeException(sprintf('Message "%s" is not registered.', $message));
        }
    }
}
