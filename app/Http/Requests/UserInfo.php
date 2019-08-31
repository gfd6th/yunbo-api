<?php

namespace App\Http\Requests;

use App\Rules\Cache;
use Illuminate\Foundation\Http\FormRequest;

class UserInfo extends FormRequest
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
            'name' => 'required',
            'phone' => 'required|digits:11',
            'group_code' => 'required|exists:groups,code',
            'openid' => ['required', new Cache],
        ];
    }
    
    public function messages()
    {
        return [
            'group_code.exists' => '口令错误',
        ];
    }
}
