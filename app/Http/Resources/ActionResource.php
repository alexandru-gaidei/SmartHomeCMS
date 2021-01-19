<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActionResource extends JsonResource
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
            'id' => $this->id,
            'is_favorite'=> ! empty($this->favorite),
            'favorite_name' => $this->favorite ? $this->favorite->name : null,
            'sensor' => $this->sensor,
            'sensor_id' => $this->sensor_id,
            'name' => $this->name,
            'value_type' => $this->value_type,
            'type' => $this->type,
            'subject' => $this->subject,
        ];
    }
}
