<?php

declare(strict_types=1);

namespace Pandawa\Bundle\PaginationBundle;

use Illuminate\Pagination\PaginationState;
use Pandawa\Component\Foundation\Bundle\Bundle;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class PaginationBundle extends Bundle
{
    public function register(): void
    {
        PaginationState::resolveUsing($this->app);
    }
}
