<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends FormRequest
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
            'course_type_id' => 'required',
            'avatar' => 'file|max:2048|mimes: jpg,png,gif,jpeg,svg',
            'is_active' => 'boolean|required',
            'translations'=>'required',
            'prices'=>'required',
            'customer_type_id'=>'integer'
        ];
    }
}
