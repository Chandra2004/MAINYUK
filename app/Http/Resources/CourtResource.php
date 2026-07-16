<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourtResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'sport_type'      => $this->sport_type,
            'location'        => $this->location,
            'city'            => $this->city,
            'address'         => $this->address,
            'price_per_hour'  => $this->price_per_hour,
            'rating'          => $this->rating,
            'total_reviews'   => $this->total_reviews,
            'description'     => $this->description,
            'facilities'      => $this->facilities ?? [],
            'images'          => $this->images ?? [],
            'courts_detail'   => $this->courts_detail ?? [],
            'open_time'       => $this->open_time,
            'close_time'      => $this->close_time,
            'main_image'      => $this->main_image,
            'formatted_price' => $this->formatted_price,
        ];
    }
}
