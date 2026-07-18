<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
            'name' => 'bail|required|max:255|min:1|string',
            'title' => 'bail|max:255|min:1|string',
            'type' => 'required|min:1|max:5|numeric',
            'phone' => 'min:10|numeric',
            'email' => 'bail|email',
            'address' => 'bail|string',
            'num_of_employee' => 'bail|numeric',
            'content' => 'bail|string',
        ];
    }
}
