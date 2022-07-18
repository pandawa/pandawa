<?php

declare(strict_types=1);

namespace Pandawa\Component\Config;

use Pandawa\Contracts\Config\ProcessorInterface;
use Symfony\Component\Config\Definition\NodeInterface;
use Symfony\Component\Config\Definition\Processor;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ConfigProcessor implements ProcessorInterface
{
    protected Processor $processor;

    public function __construct()
    {
        $this->processor = new Processor();
    }

    public function process(NodeInterface $configTree, array $configs): array
    {
        return $this->processor->process($configTree, $configs);
    }
}
