<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
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
            'affiliate' => $this->affiliate,
            'profit' => $this->profit,
            'paid_stat' => $this->paid_stat,
            'code' => $this->code,
            'members' => UserForOwnerResource::collection($this->whenLoaded('members'))
        ];
    }
}
