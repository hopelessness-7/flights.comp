<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FlightResource extends JsonResource
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
            'flight_code' => $this->flight_code,
            'from_id' => new AirportResource($this->from_airport),
            'to_id' => new AirportResource($this->to_airport),
            'time_from' => $this->time_from,
            'time_to' => $this->time_to,
            'cost' => $this->cost,
            'date' => $this->date,
            'count_passengers' => $this->count_passengers,
        ];
    }
}
