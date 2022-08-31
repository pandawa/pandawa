<?php

declare(strict_types=1);

namespace Pandawa\Component\Eloquent;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;
use Pandawa\Component\Eloquent\Traits\HasAttributesTrait;
use Pandawa\Component\Eloquent\Traits\HasRelationshipsTrait;
use Pandawa\Component\Eloquent\Traits\HasSaveCallbacksTrait;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
abstract class Model extends Eloquent
{
    use HasRelationshipsTrait,
        HasAttributesTrait,
        HasSaveCallbacksTrait;

    protected static ?string $resourceName = null;

    /**
     * Guarded fields.
     *
     * @var string[]
     */
    protected $guarded = [];

    /**
     * Hidden fields.
     *
     * @var string[]
     */
    protected $hidden = ['pivot'];

    public static function resourceName(): string
    {
        if (null !== $resourceName = static::$resourceName) {
            return $resourceName;
        }

        $name = substr(static::class, (int)strrpos(static::class, '\\') + 1);

        return Str::snake($name);
    }

    public function hasRelation(string $name): bool
    {
        return method_exists($this, $name) && $this->{$name}() instanceof Relation;
    }
}
