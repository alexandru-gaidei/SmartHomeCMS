<?php

namespace App\Http\Resources;

use App\History;
use App\Sensor;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Recurr\Rule;
use Recurr\Transformer\TextTransformer;

class SensorResource extends JsonResource
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

        $rrule_freq = $rrule_interval = $rrule_human = null;

        if (! empty($this->execute_at_rrule)) {
            $rule = Rule::createFromString($this->execute_at_rrule);
            $textTransformer = new TextTransformer();

            $rrule_freq = $rule->getFreqAsText();
            $rrule_interval = $rule->getInterval();
            $rrule_human = $textTransformer->transform($rule);
        }

        $data['rrule_freq'] = $rrule_freq;
        $data['rrule_interval'] = $rrule_interval;
        $data['rrule_human'] = $rrule_human;

        $history_chart = $this->history()->select(['ocurrence_at', 'value'])->orderBy('ocurrence_at')
            ->where('value' , '!=', '')
            ->where('ocurrence_at', '>=', now()->subDays(History::CHART_DAYS_AGO))->get();

        $data['history_last'] = $this->history()->orderBy('ocurrence_at', 'DESC')->take(10)->get();

        $history_chart = $history_chart->map(function($h) {
            $h->groupped_by = Carbon::parse($h->ocurrence_at)->format('Y-m-d H:00:00');
            return $h;
        })->groupBy('groupped_by')->map(function($value, $key) {
            $obj = new \stdClass();
            $obj->ocurrence_at = $key;
            $obj->value = round($value->sum('value') / $value->count(), 1);
            return $obj;
        })->values();

        $data['history_chart'] = [
            'labels' => $history_chart->pluck('ocurrence_at')->map(function ($occurence) {
                return Carbon::parse($occurence)->format('d/m H:i');
            })->toArray(),
            'data' => $history_chart->pluck('value')->toArray(),
        ];

        return $data;
    }
}
