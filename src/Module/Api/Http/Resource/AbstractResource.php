<?php
declare(strict_types=1);

namespace Pandawa\Module\Api\Http\Resource;

use Illuminate\Container\Container;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Collection;
use JsonSerializable;
use Pandawa\Module\Api\Http\Response\ResourceResponse;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
abstract class AbstractResource extends Resource
{
    /**
     * {@inheritdoc}
     */
    public function resolve($request = null)
    {
        $data = $this->toArray(
            $request = $request ?: Container::getInstance()->make('request')
        );

        if (is_array($data)) {
            $data = $data;
        } elseif ($data instanceof Arrayable || $data instanceof Collection) {
            $data = $data->toArray();
        } elseif ($data instanceof JsonSerializable) {
            $data = $data->jsonSerialize();
        } elseif (null === $data || is_scalar($data)) {
            return $data;
        }

        return $this->filter((array) $data);
    }

    /**
     * {@inheritdoc}
     */
    public function toResponse($request)
    {
        return (new ResourceResponse($this))->toResponse($request);
    }
}
