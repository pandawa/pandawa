<?php
declare(strict_types=1);

namespace Pandawa\Module\Api\Query;

use Illuminate\Http\Request;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class FindAuthenticatedHandler
{
    /**
     * @var Request
     */
    private $request;

    /**
     * Constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        return $this->request->user();
    }
}
