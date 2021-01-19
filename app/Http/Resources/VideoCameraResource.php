<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VideoCameraResource extends JsonResource
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
            'group_id' => $this->group_id,
            'name' => $this->name,
            'stream_url' => $this->stream_url,
            'store' => $this->store,
            'keep_days' => $this->keep_days,
            'length' => $this->length,
            'size_height' => $this->size_height,
            'pid' => $this->pid,
            'is_rec' => $this->pid && in_array($this->pid, $this->processes())
        ];
    }
}
