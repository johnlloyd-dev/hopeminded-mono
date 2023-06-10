<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeacherRequest;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function getTeachers() {
        return User::join('teachers', function (JoinClause $join) {
            $join->on('users.id', '=', 'teachers.user_id');
        })
        ->select('users.*', 'teachers.*')
        ->get();
    }

    public function addTeacher(TeacherRequest $request) {
        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'user_flag' => 'teacher'
        ]);
        $teacher = Teacher::create([
            'first_name' => $request->firstName,
            'middle_name' => $request->middleName,
            'last_name' => $request->lastName,
            'email' => $request->email,
            'user_id' => $user->id
        ]);

        if($teacher) {
            return response()->json([
                'message' => 'A teacher has been added successfully.'
            ]);
        }
    }

    public function getStudents($teacherId) {
        return Student::where('teacher_id', $teacherId)->get();
    }
}
