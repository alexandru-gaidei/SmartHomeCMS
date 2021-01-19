<?php

namespace App\Http\Requests;

use App\Sensor;
use Illuminate\Foundation\Http\FormRequest;

class SensorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $is_editing = $this->isMethod('patch');
        return [
            'group_id' => 'required|exists:groups,id',
            'name' => 'required|max:255',
            'favorite_name' => 'required_if:is_favorite,1|max:255',
            'source_type' => 'required|in:' . implode(',', array_keys(Sensor::$SRC_TYPES)),
            'source_url_fetch' => 'required_if:source_type,'. Sensor::SRC_TYPE_FETCH,
            'parameter' => 'required|max:255',
            'identifier' => 'required_if:source_type,'. Sensor::SRC_TYPE_PUSH .
                '|max:255|unique:sensors,identifier' . ($is_editing ? ',' . $this->sensor->id : ''),
            'value_type' => 'required|in:' . implode(',', array_keys(Sensor::$VAL_TYPES)),
            'execute_at_rrule' => 'required_if:source_type,'. Sensor::SRC_TYPE_FETCH,
            'min_value' => 'numeric',
            'max_value' => 'numeric'
        ];
    }
}
