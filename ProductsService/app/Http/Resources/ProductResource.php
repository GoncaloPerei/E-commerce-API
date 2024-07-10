<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\ProductStatusResource;

class ProductResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'price' => number_format($this->price, 2, '.', ''),
            'image' => $this->image,
            'stock' => $this->stock,
            'category' => $this->category,
            'status' => new ProductStatusResource($this->status),
            'createdAt' => date('Y-m-d H:i:s', strtotime($this->created_at)),
            'updatedAt' => date('Y-m-d H:i:s', strtotime($this->updated_at)),
            'deletedAt' => date('Y-m-d H:i:s', strtotime($this->deleted_at)),
        ];
    }
}
