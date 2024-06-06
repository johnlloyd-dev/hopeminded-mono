<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeacherRequest;
use App\Mail\UserCredentialsMail;
use App\Models\PassingPercentage;
use App\Models\QuantityRequirement;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class TeacherController extends Controller
{
    public function getTeachers()
    {
        return Teacher::leftJoin('users', function ($join) {
            $join->on('users.id', '=', 'teachers.user_id');
        })
            ->select('users.status', 'teachers.*')
            ->get();

        // return Teacher::get();
    }

    public function addTeacher(TeacherRequest $request)
    {
        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'user_flag' => 'teacher'
        ]);

        $teacher = Teacher::create([
            // 'access_id' => 'HM-' . $request->accessId,
            'first_name' => $request->firstName,
            'middle_name' => $request->middleName,
            'last_name' => $request->lastName,
            'email' => $request->email,
            'user_id' => $user->id,
            // 'unhashed' => $request->password
        ]);

        $defaultPercentage = [
            [
                'teacher_id' => $teacher->id,
                'flag' => 'skill_test',
                'percentage' => 75
            ],
            [
                'teacher_id' => $teacher->id,
                'flag' => 'quiz',
                'percentage' => 75
            ],
            [
                'teacher_id' => $teacher->id,
                'flag' => 'overall',
                'percentage' => 75
            ],
        ];

        $defaultSkillTestSubmission = [
            [
                'teacher_id' => $teacher->id,
                'flag' => 'alphabet',
                'value' => 26
            ],
            [
                'teacher_id' => $teacher->id,
                'flag' => 'number',
                'value' => 10
            ],
        ];

        $mailData = [
            'username' => $user->username,
            'password' => $request->password
        ];

        PassingPercentage::insert($defaultPercentage);
        QuantityRequirement::insert($defaultSkillTestSubmission);
        Mail::to($request->email)->send(new UserCredentialsMail($mailData));

        if ($teacher) {
            return response()->json([
                'message' => 'A teacher has been added successfully.'
            ]);
        }
    }

    public function getStudents($teacherId)
    {
        return Student::where('teacher_id', $teacherId)->get();
    }
}
