<?php
declare(strict_types=1);

namespace Pandawa\Module\Api\Http\Controller;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface PresenterHandlerInterface
{
    /**
     * @return View|Responsable
     */
    public function __invoke(Request $request);
}