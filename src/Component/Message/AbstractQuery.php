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
     * @var array
     */
    private $relations;

    /**
     * @var array
     */
    private $specifications;

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

    /**
     * @param array $relations
     *
     * @return AbstractQuery
     */
    final public function withRelations(array $relations): AbstractQuery
    {
        $this->relations = $relations;

        return $this;
    }

    /**
     * @param array $specifications
     *
     * @return AbstractQuery
     */
    final public function withSpecifications(array $specifications): AbstractQuery
    {
        $this->specifications = $specifications;

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

    public function hasRelations(): bool
    {
        return null !== $this->relations;
    }

    public function getRelations(): array
    {
        return $this->relations ?: [];
    }

    public function hasSpecifications(): bool
    {
        return null !== $this->specifications;
    }

    public function getSpecifications(): array
    {
        return $this->specifications ?: [];
    }
}
