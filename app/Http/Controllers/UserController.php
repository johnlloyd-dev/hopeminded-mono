<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentRequest;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function getAuth() {
        return Auth::user();
    }

    public function getStudents() {
        $auth = Auth::user();
        $teacher = Teacher::where('user_id', $auth->id)->first();
        return Student::where('teacher_id', $teacher->id)->get();
    }

    public function addStudent(StudentRequest $request) {
        $auth = Auth::user();
        $teacher = Teacher::where('user_id', $auth->id)->first();
        $user = Student::create([
            'access_id' => 'HM-'.$request->accessId,
            'first_name' => $request->firstName,
            'middle_name' => $request->middleName,
            'last_name' => $request->lastName,
            'teacher_id' => (int)$teacher->id
        ]);

        if($user) {
            return response()->json([
                'message' => 'A student has been added successfully.'
            ]);
        }
    }

    public function editStudent() {

    }

    public function deleteStudent() {

    }
}
