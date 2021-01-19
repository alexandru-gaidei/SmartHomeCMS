<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VideoCameraRecordResource extends JsonResource
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
            'id'               => $this['id'],
            'filename'         => $this['filename'],
            'url'              => $this['url'],
            'path'             => $this['path'],
            'created_at' => $this['created_at']
        ];
    }
}
