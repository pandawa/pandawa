<?php
declare(strict_types=1);

namespace Pandawa\Module\Api\Query;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class FindAuthenticatedHandler
{

    public function handle()
    {
        return request()->user();
    }
}
