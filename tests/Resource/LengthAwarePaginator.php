<?php

declare(strict_types=1);

namespace Test\Resource;

use Illuminate\Pagination\LengthAwarePaginator as LaravelLengthAwarePaginator;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class LengthAwarePaginator extends LaravelLengthAwarePaginator
{
    public function linkCollection()
    {
        return collect($this->elements())->flatMap(function ($item) {
            if (! is_array($item)) {
                return [['url' => null, 'label' => '...', 'active' => false]];
            }

            return collect($item)->map(function ($url, $page) {
                return [
                    'url' => $url,
                    'label' => (string) $page,
                    'active' => $this->currentPage() === $page,
                ];
            });
        })->prepend([
            'url' => $this->previousPageUrl(),
            'label' => 'Previous',
            'active' => false,
        ])->push([
            'url' => $this->nextPageUrl(),
            'label' => 'Next',
            'active' => false,
        ]);
    }
}
