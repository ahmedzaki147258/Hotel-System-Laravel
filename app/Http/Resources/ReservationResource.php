<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
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
            'client_id' => $this->client_id,
            // 'room_id' => $this->room_id,
            'accompany_number' => $this->accompany_number,
            'paid_price_in_cents' => $this->paid_price_in_cents,
            'paid_price_in_dollars' => '$' . number_format($this->paid_price_in_cents / 100, 2),
            'payment_id' => $this->payment_id,
            'created_at' => $this->created_at,
            'check_out_at' => $this->check_out_at,
        ];
    }
}
