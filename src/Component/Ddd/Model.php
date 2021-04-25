<?php

declare(strict_types=1);

namespace Pandawa\Component\Ddd;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface Model
{
    public static function getResourceName(): string;

    public static function getModelClass(): ?string;

    public static function getRepositoryClass(): ?string;

    public function getMappedModel();
}
