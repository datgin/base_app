<?php

namespace App\Http\Resources\Example;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ExampleCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (
            $this->resource instanceof \Illuminate\Pagination\LengthAwarePaginator ||
            $this->resource instanceof \Illuminate\Pagination\Paginator
        ) {
            return [
                'items' => ExampleResource::collection($this->collection),
                'total' => $this->total(),
            ];
        }
        return ExampleResource::collection($this->collection)->resolve();
    }
}
