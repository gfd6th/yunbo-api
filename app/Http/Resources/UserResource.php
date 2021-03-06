<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'forbidden' => $this->forbidden,
            'expire_at' => $this->expire_at,
            'lifetime'  => $this->lifetime,
            'isPremium' => $this->isPremium(),
            'isSubscribe' => $this->isSubscribe
        ];
    }
}
