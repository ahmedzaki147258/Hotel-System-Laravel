<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
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
            'number' => $this->number,
            'capacity' => $this->capacity,
            'status' => $this->status,
            'price' => '$' . number_format($this->price / 100, 2),
            'manager_id' => $this->manager_id,
            'floor_id' => $this->floor_id,
            'floor' => new FloorResource($this->floor),
        ];
    }
}
