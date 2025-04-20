<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'avatar_image' => asset('storage/' . $this->avatar_image),
            'country_id' => $this->country_id,
            'country' => $this->country['translations'][0]['name'],
            'gender' => $this->gender,
            'mobile' => $this->mobile,
        ];
    }
}
