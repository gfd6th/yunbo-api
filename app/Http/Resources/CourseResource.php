<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            'id'          => $this->id,
            'title'       => $this->title,
            'lessonCount' => $this->lessons_count ?? $this->whenLoaded('lessons')->count(),
            'lessons'     => LessonResource::collection($this->whenLoaded('lessons')),
            'img'         => $this->img,
            'intro'       => $this->intro,
            'free'        => $this->free,
            'level'       => $this->level,
        ];
    }


}
