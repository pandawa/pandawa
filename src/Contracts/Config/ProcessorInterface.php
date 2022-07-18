<?php

declare(strict_types=1);

namespace Pandawa\Contracts\Config;

use Symfony\Component\Config\Definition\NodeInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface ProcessorInterface
{
    public function process(NodeInterface $configTree, array $configs): array;
}
