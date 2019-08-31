<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserMoreInfoResource extends JsonResource
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
            'phone'     => $this->phone,
            'ownGroup'  => $this->when($this->ownGroup()->count() > 0, GroupResource::collection($this->ownGroup->load('members'))),
            'group'     => $this->group ? $this->group->name : null,
            'avatar'    => $this->avatar,
        ];
    }
}
