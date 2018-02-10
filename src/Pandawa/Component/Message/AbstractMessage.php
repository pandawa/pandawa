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
abstract class AbstractMessage
{
    use PayloadTrait;

    final public function __construct(array $payload = [])
    {
        $this->init($payload);
        $this->setPayload($payload);
    }

    /**
     * {@inheritdoc}
     */
    protected function init(array &$payload): void
    {
        // Override this method to custom initialization
    }
}
