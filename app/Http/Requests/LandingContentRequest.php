<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LandingContentRequest extends FormRequest
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
            'image' => 'file|max:1024|mimes:svg,jpg,png,gif,jpeg',
            'parent_id' => 'numeric',
            'landing_type_id' => 'numeric',
            'landing_page_id' => 'numeric',
            'sort' => 'numeric',
            'is_active' => 'boolean',
            'translations' => 'required',
        ];
    }
}
