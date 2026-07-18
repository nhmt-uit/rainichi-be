<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClassroomRequest extends FormRequest
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
            'name' => 'required|string',
            'num_of_employee' => 'required|numeric|min:1',
            'level_id' => 'numeric',
            'company_id' => 'required|numeric',
            'admin_id' => 'numeric',
            'credits' => 'numeric',
            'is_active' => 'boolean',
        ];
    }
}
