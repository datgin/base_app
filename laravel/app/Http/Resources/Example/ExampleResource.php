<?php

namespace App\Http\Resources\Example;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExampleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'image' => showImage($this->image),
            'albums' => $this->albums
                ? collect($this->albums)->map(fn($image) => showImage($image))->toArray()
                : [],
            'date' => $this->date?->format('d/m/Y'),
            'status' => $this->status,
            'description' => $this->description,
            'content' => $this->content,
            'views' => $this->views,
            'is_active' => $this->is_active,
            'price' =>  formatPrice($this->price),
            'published' => $this->published,
            'archive' => $this->archive,
            'created_at' =>  $this->created_at?->format('d/m/Y H:i'),
        ];
    }
}
