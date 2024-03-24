<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToModel, WithHeadingRow
{
    use Importable;

    public function model(array $row)
    {
        $email_exist = Student::where('email', $row['email'])->first();

        if ($email_exist) {
            return null;
        }

        $auth = Auth::user();
        $teacher = Teacher::where('user_id', $auth->id)->first();

        $defaultUsername = "";
        if (isset($row['last_name'])) {
            $defaultUsername .= strtolower($row['last_name']) . "_";
        }

        $defaultUsername .= rand(100000, 999999);
        $defaultPassword = "password";

        $user = User::create([
            'username' => preg_replace('/\s+/', '_', $defaultUsername),
            'password' => Hash::make($defaultPassword),
            'user_flag' => 'student'
        ]);

        return new Student([
            'user_id' => $user->id,
            'teacher_id' => (int)$teacher->id,
            'first_name' => $row['first_name'],
            'middle_name' => $row['middle_name'],
            'last_name' => $row['last_name'],
            'gender' => $row['gender'],
            'email' => $row['email']
        ]);
    }
}
