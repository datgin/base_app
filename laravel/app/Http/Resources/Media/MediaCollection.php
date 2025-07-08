<?php

namespace App\Http\Resources\Media;

use App\Http\Resources\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MediaCollection extends ResourceCollection
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
                'items' => MediaResource::collection($this->collection),
                'total' => $this->total(),
            ];
        }
        return parent::toArray($request);
    }
}
