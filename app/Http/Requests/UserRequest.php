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
        return [
            'avatar' => 'bail|file|max:10240|mimes: jpg,png,gif,jpeg,svg',
            'email' => 'bail|email|unique:users,email',
            'name'  => 'bail|string'
        ];
    }
}
