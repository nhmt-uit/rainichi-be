<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AlphabetRequest extends FormRequest
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
            'character' => 'required',
            'audio' => 'file|max:2048|mimes: mp3,m4a, wav, wma,mpga',
            'image' => 'file|max:1024|mimes: jpg,png,gif,jpeg,svg',
            'translations' => 'required'
        ];
    }
}
