<?php
declare(strict_types=1);

namespace Pandawa\Module\Api\Http\Response;

use Illuminate\Http\Resources\Json\ResourceResponse as LaravelResourceResponse;
use Illuminate\Support\Collection;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ResourceResponse extends LaravelResourceResponse
{
    protected function wrap($data, $with = [], $additional = [])
    {
        if ($data instanceof Collection) {
            $data = $data->all();
        }

        if (null === $data || is_scalar($data)) {
            $data = [($this->wrapper() ?? 'data') => $data];
        } elseif ($this->haveDefaultWrapperAndDataIsUnwrapped($data)) {
            $data = [$this->wrapper() => $data];
        } elseif ($this->haveAdditionalInformationAndDataIsUnwrapped($data, $with, $additional)) {
            $data = [($this->wrapper() ?? 'data') => $data];
        }

        return array_merge_recursive($data, $with, $additional);
    }
}
