<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TextbookNumberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'value' => 'required|numeric|between:1,10|unique:textbook_numbers,value',
            'object_name' => 'required',
            'image' => 'required|mimes:jpeg,png,jpg,gif',
            'video' => 'required|mimetypes:video/avi,video/mpeg,video/quicktime,video/mp4'
        ];
    }
}
