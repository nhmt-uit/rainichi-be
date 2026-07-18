<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfigurationRequest extends FormRequest
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
            'logo' => 'bail|file|max:1024|mimes: jpg,png,gif,jpeg,svg',
            'is_active' => 'boolean',
            'phone' => 'numeric',
            'email' => 'email',
            'longitude' => 'numeric',
            'latitude' => 'numeric',
            'currency' => 'numeric',
        ];
    }
}
