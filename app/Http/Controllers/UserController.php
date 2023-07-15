<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentRequest;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function getAuth()
    {
        return Auth::user();
    }

    public function getStudents()
    {
        return User::join('students', function (JoinClause $join) {
            $join->on('users.id', '=', 'students.user_id');
        })
            ->select('users.*', 'students.*')
            ->get();
    }

    public function addStudent(StudentRequest $request)
    {
        $auth = Auth::user();
        $teacher = Teacher::where('user_id', $auth->id)->first();
        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'user_flag' => 'student'
        ]);
        $user = Student::create([
            'user_id' => $user->id,
            'first_name' => $request->firstName,
            'middle_name' => $request->middleName,
            'last_name' => $request->lastName,
            'gender' => $request->gender,
            'email' => $request->email,
            'teacher_id' => (int)$teacher->id,
            'unhashed' => $request->password
        ]);

        if ($user) {
            return response()->json([
                'message' => 'A student has been added successfully.'
            ]);
        }
    }

    public function editStudent()
    {
    }

    public function deleteStudent()
    {
    }
}
