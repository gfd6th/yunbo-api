<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $video = '';
        if($this->free){
            $video = $this->video;
        }else{
            if (auth()->check() && auth()->user()->isPremium()){
                $video = $this->video;
            }
        }

        return [

            'id' => $this->id,
            'title' => $this->title,
            'intro' => $this->intro,
            'video' => $video,
            'free' => $this->free,
        ];
    }
}
