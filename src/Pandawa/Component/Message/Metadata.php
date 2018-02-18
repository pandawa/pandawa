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

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Metadata
{
    /**
     * @var string
     */
    private $messageClass;

    /**
     * @var string
     */
    private $handlerClass;

    /**
     * Constructor.
     *
     * @param string $messageClass
     * @param string $handlerClass
     */
    public function __construct(string $messageClass, string $handlerClass = null)
    {
        $this->messageClass = $messageClass;
        $this->handlerClass = $handlerClass;
    }

    public function getMessageClass(): string
    {
        return $this->messageClass;
    }

    public function getHandlerClass(): ?string
    {
        return $this->handlerClass;
    }
}
