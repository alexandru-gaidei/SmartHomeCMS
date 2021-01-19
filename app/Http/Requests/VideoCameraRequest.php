<?php

namespace App\Http\Requests;

use App\VideoCamera;
use Illuminate\Foundation\Http\FormRequest;

class VideoCameraRequest extends FormRequest
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
            'group_id'    => 'required|exists:groups,id',
            'name'        => 'required|max:255',
            'stream_url'  => 'required|max:255',
            'store'       => 'required|boolean',
            'length'      => 'numeric|required_if:store,1',
            'size_height' => 'numeric|required_if:store,1|in:' . implode(',', array_keys(VideoCamera::$size_heights)),
            'keep_days'   => 'numeric|required_if:store,1'
        ];
    }
}
