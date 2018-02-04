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
abstract class AbstractQuery extends AbstractMessage
{
    const DEFAULT_PAGE_SIZE = 50;

    /**
     * @var int
     */
    private $pageSize;

    /**
     * @param int $pageSize
     *
     * @return static
     */
    final public function paginate(int $pageSize = self::DEFAULT_PAGE_SIZE): AbstractQuery
    {
        $this->pageSize = $pageSize;

        return $this;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function isPaginated(): bool
    {
        return null !== $this->pageSize;
    }
}
