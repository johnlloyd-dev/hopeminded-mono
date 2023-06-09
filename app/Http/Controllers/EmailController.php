<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordResetRequest;
use App\Mail\ResetPasswordMail;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function sendResetPasswordLink(Request $request) {
        $mailData = [];
        $teacher = Teacher::where('email', $request->email)->get();
        $student = Student::where('email', $request->email)->where('is_registered', 1)->get();

        if(count($teacher) === 0 && count($student) === 0) {
            return response()->json([
                'email' => ['The entered email address is not registered.']
            ], 422);
        } else {
            $mailData = [
                "name" => count($teacher) != 0 ? $teacher[0]->first_name : $student[0]->first_name,
                "email" => $request->email
            ];
        }

        Mail::to($request->email)->send(new ResetPasswordMail($mailData));
        return response()->json([
            'message' => 'Reset password link has been sent successfully.'
        ]);
    }

    public function resetPassword(PasswordResetRequest $request) {
        $teacher = Teacher::where('email', $request->email)->get();
        $student = Student::where('email', $request->email)->where('is_registered', 1)->get();

        if(count($teacher) === 0 && count($student) === 0) {
            return response()->json([
                'errors' => [
                    'email' => ['The entered email address is not registered.']
                ]
            ], 422);
        } else {
            if (count($teacher) != 0) {
                $user = User::find($teacher[0]->user_id)->update([
                    'password' => Hash::make($request->password)
                ]);
            } else {
                $user = User::find($student[0]->user_id)->update([
                    'password' => Hash::make($request->password)
                ]);
            }
        }

        if($user) {
            return response()->json([
                'message' => 'Your password has been updated.'
            ]);
        }
    }
}
