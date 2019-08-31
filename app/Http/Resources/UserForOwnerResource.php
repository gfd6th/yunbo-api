<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserForOwnerResource extends UserResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'forbidden' => $this->forbidden,
            'expire_at' => $this->expire_at,
            'lifetime' => $this->lifetime,
        ];
    }
}
