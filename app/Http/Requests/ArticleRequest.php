<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
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
            'image' => 'file|max:10240|mimes: jpg,png,gif,jpeg,svg',
            'category_id' => 'numeric',
            'is_active' => 'boolean',
            'translations' => 'required',
        ];
    }
}
