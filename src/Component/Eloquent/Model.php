<?php

declare(strict_types=1);

namespace Pandawa\Component\Eloquent;

use Illuminate\Database\Eloquent\Model as Eloquent;
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
}
