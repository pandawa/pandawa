<?php

declare(strict_types=1);

namespace Pandawa\Component\Routing;

use InvalidArgumentException;
use Pandawa\Contracts\Routing\GroupRegistryInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class GroupRegistry implements GroupRegistryInterface
{
    protected array $groups = [];

    public function __construct(iterable $groups = [])
    {
        foreach ($groups as $group => $options) {
            $this->add($group, $options);
        }
    }

    public function add(string $group, array $options): void
    {
        $this->groups[$group] = $options;
    }

    public function has(string $group): bool
    {
        return array_key_exists($group, $this->groups);
    }

    public function get(string $group): array
    {
        if ($this->has($group)) {
            return $this->groups[$group];
        }

        throw new InvalidArgumentException(sprintf('Group "%s" is not found.', $group));
    }
}
