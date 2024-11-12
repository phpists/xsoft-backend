<?php

namespace App\Http\Resources;

use Illuminate\Pagination\LengthAwarePaginator;

trait HasResourceCollection
{
    /**
     * Возвращает коллекцию, преобразованную в callback
     * Со стандартной пагинацией
     * @param $callback
     * @return array
     */
    protected function returnResource($callback)
    {
        /** @var LengthAwarePaginator $resource */
        $resource = $this->resource;

        $items = $resource->map($callback)->all();
        $return = $items;

        return $return;
    }
}
