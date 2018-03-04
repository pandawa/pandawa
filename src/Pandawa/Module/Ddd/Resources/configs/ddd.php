<?php
/**
 * This file is part of the Pandawa package.
 *
 * (c) 2018 Pandawa <https://github.com/bl4ckbon3/pandawa>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    'entity_manager_class'         => Pandawa\Component\Ddd\Repository\EntityManager::class,
    'default_repository_class'     => Pandawa\Component\Ddd\Repository\Repository::class,
    'specification_registry_class' => Pandawa\Component\Ddd\Specification\SpecificationRegistry::class,
];
