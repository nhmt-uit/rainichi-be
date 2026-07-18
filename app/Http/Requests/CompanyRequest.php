<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
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
            'image' => 'file|max:1024|mimes: jpg,png,gif,jpeg,svg',
            'num_of_employee' => 'required|numeric',
            'phone' => 'required|numeric',
            'fax' => 'numeric',
            'sort_order' => 'numeric',
            'email' => 'email|unique:companies',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Name is required',
            'phone.required' => 'Phone is required',
            'career.required' => 'Career is required',
            'num_of_employee.required'  => 'Num of employee is required',
        ];
    }
}
