<?php

namespace App\Http\Requests;

use App\Action;
use Illuminate\Foundation\Http\FormRequest;

class ActionRequest extends FormRequest
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
        return [
            'sensor_id' => 'required|exists:sensors,id',
            'name' => 'required|max:255',
            'favorite_name' => 'required_if:is_favorite,1|max:255',
            'value_type' => 'required|in:' . implode(',', array_keys(Action::$VAL_TYPES)),
            'type' => 'required|in:' . implode(',', array_keys(Action::$TYPES)),
            'subject' => 'required_if:type,'. Action::TYPE_HTTP_GET .'|max:255',
        ];
    }
}
