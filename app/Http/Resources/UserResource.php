<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'personal_data' =>  new UserDataResource($this->user_data),
            'email' => $this->email,
            'phone' => $this->phone,
            'email_verified_at' => $this->email_verified_at
        ];
    }
}
