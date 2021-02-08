<?php

namespace App\Http\Resources;

use App\History;
use App\Sensor;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Recurr\Rule;
use Recurr\Transformer\TextTransformer;

class SensorShortResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $push_url = null;
        if ($this->source_type === Sensor::SRC_TYPE_PUSH) {
            $push_url = route('push', $this->identifier);
        }

        $data = [
            'id' => $this->id,
            'group' => $this->group,
            'group_id' => $this->group_id,
            'is_favorite' => ! empty($this->favorite),
            'name' => $this->name,
            'favorite_name' => $this->favorite ? $this->favorite->name : null,
            'source_type' => $this->source_type,
            'source_url_fetch' => $this->source_url_fetch,
            'parameter' => $this->parameter,
            'identifier' => $this->identifier,
            'push_url' => $push_url,
            'value_type' => $this->value_type,
            'execute_at_rrule' => $this->execute_at_rrule,
            'min_value' => $this->min_value,
            'value' => $this->value,
            'max_value' => $this->max_value,
            'actions' => $this->actions,
        ];

        return $data;
    }
}
