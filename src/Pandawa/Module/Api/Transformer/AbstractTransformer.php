<?php
/**
 * This file is part of the Pandawa package.
 *
 * (c) 2018 Pandawa <https://github.com/bl4ckbon3/pandawa>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Pandawa\Module\Api\Transformer;

use Illuminate\Http\Resources\Json\Resource;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
abstract class AbstractTransformer extends Resource
{
    public function with($request)
    {
        $meta = [];
        $version = $this->version($request);
        $hostname = $this->hostname();

        if ($version) {
            $meta['version'] = $version;
        }

        if ($hostname) {
            $meta['hostname'] = $hostname;
        }

        if ($meta) {
            return ['meta' => $meta];
        }

        return [];
    }

    protected function version($request): string
    {
        $version = $request->route('version');

        if (!$version && config('api.default_version')) {
            $version = config('api.default_version');
        }

        return (string) $version;
    }

    protected function hostname(): ?string
    {
        if (true === config('api.show_hostname')) {
            return gethostname();
        }

        return null;
    }
}
