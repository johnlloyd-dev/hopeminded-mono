<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeacherRequest extends FormRequest
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
            'accessId' => 'required|unique:teachers,access_id',
            'firstName' => 'required',
            'middleName' => 'required',
            'lastName' => 'required'
            // 'email' => 'required|unique:teachers,email',
            // 'username' => 'required|unique:users,username',
            // 'password' => 'required',1
        ];
    }
}
