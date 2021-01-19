<?php

namespace App\Http\Resources;

use App\Action;
use App\Favorite;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
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
            'order' => $this->order,
            'name' => $this->name,
            'type' => $this->favoriteable_type === Action::class ? Favorite::TYPE_ACTION : Favorite::TYPE_SENSOR,
            'favoriteable' => $this->favoriteable_type === Action::class
                ? new ActionResource($this->favoriteable)
                : new SensorResource($this->favoriteable)
        ];
    }
}
