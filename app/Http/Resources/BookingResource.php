<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'flight_from' => new FlightResource($this->flight_from_to_booking),
            'flight_back' => new FlightResource($this->flight_back_to_booking),
            'date_from' => $this->date_from,
            'date_back' => $this->date_back,
            'code' => $this->code,
            'passengers' => PassengerResource::collection($this->passengers),
        ];
    }
}
