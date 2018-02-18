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

namespace Pandawa\Component\Resource;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Metadata
{
    /**
     * @var string
     */
    private $modelClass;

    /**
     * @var array
     */
    private $options;

    /**
     * Constructor.
     *
     * @param string $modelClass
     * @param array  $options
     */
    public function __construct(string $modelClass, array $options = [])
    {
        $this->modelClass = $modelClass;
        $this->options = $options;
    }

    public function getModelClass(): string
    {
        return $this->modelClass;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
