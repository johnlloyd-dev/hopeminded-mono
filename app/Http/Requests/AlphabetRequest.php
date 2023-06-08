<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AlphabetRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'letter' => 'required',
            'objectName' => 'required|min:1',
            'image' => 'required|min:1',
            'video' => 'required|min:1',
        ];
    }

    public function messages()
    {
        return [
            'objectName.min' => 'Object name field must have atleast one value.',
            // 'objectName.max' => 'Object name field must have atleast one value.',
            'image.min' => 'Image field must have atleast one value.',
            // 'image.max' => 'Image field must have atleast one value.',
            'video.min' => 'Video field must have atleast one value.',
            // 'video.max' => 'Video field must have atleast one value.',
        ];
    }
}
