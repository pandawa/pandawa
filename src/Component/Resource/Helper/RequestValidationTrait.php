<?php

declare(strict_types=1);

namespace Pandawa\Component\Resource\Helper;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait RequestValidationTrait
{
    protected function validateRequest(Request $request): array
    {
        $data = $this->allRequestData($request);

        if (!empty($rules = $this->getRouteOption('rules', $request))) {
            $filtered = [];

            foreach ($rules as $rule) {
                $validator = $this->validationFactory->create($rule, $data);

                $filtered = [...$filtered, ...$validator->validated()];
            }

            return $filtered;
        }

        return $data;
    }

    protected function allRequestData(Request $request): array
    {
        return [
            ...$request->all(),
            ...$request->files->all(),
            ...$this->allRouteParameters($request),
        ];
    }

    protected function allRouteParameters(Request $request): array
    {
        return Arr::except($request->route()->parameters(), [
            'type',
            'middleware',
            'resource',
            'message',
            'criteria',
            'name',
            'version',
        ]);
    }
}
