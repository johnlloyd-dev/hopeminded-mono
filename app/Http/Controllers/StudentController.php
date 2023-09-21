<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function getProfile(Request $request)
    {
        if ($request->query('student_id')) {
            return Student::where('id', $request->query('student_id'))
                ->first();
        } else {
            return Student::select('students.*', 'users.*')
                ->join('users', 'students.user_id', '=', 'users.id')
                ->where('students.user_id', Auth::user()->id)
                ->first();
        }
    }

    public function updateProfile(ProfileUpdateRequest $request)
    {
        $student = Student::where('user_id', Auth::user()->id)->first();
        $student->update([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
            'email' => $request->email,
        ]);
        $student->fresh();
        $user = User::find($student->user_id);
        $user->update([
            'username' => $request->username,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' => 'Your profile has been updated successfully.'
        ]);
    }
}