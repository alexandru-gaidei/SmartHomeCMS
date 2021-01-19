<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        $email_rule = 'required|email|max:255|unique:users,email' . ($is_editing ? ',' . $this->user->id : '');
        $password_rule = ($is_editing ? 'sometimes|' : '') . 'required|min:6|max:255';

        return [
            'name'     => 'required|max:255',
            'email'    => $email_rule,
            'password' => $password_rule,
            'groups'   => 'required|array|exists:groups,id'
        ];
    }
}
