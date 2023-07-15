<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
    public function rules()
    {
        return [
            'firstName' => 'required',
            'middleName' => 'required',
            'lastName' => 'required',
            'userFlag' => 'required',
            // 'gender' => 'required_if:userFlag,student',
            'email' => 'required|email|unique:students,email',
            'username' => 'required|unique:users,username',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required|min:8',
            'teacherId' => 'nullable'
        ];
    }

    public function messages()
    {
        return [
            'gender.required_if' => 'The gender field is required.',
        ];
    }
}
