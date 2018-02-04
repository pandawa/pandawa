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
        $this->setPayload($payload);
        $this->init();
    }

    /**
     * Use this method to initialize message with defaults or extend your class
     */
    protected function init(): void
    {
    }

}
