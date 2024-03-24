<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Game;
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

    public function updateStudentSection(Student $student, Request $request)
    {
        $student->section_id = $request->input('section_id');
        $student->save();

        return response()->json([
            'message' => 'Student section has been updated successfully.'
        ]);
    }

    public function getTop10Students()
    {
        return $quizReport = Student::join('quiz_reports', 'students.id', '=', 'quiz_reports.student_id')
            ->where('flag', 'quiz')
            ->orderByRaw('-total_score ASC')
            ->get()
            ->map(function ($report) {
                $perfect_score = Game::find($report->game_id)->perfect_score;
                $percentage = ((int)$report->total_score / $perfect_score) * 100;

                $report->perfect_score = $perfect_score;
                $report->percentage = round($percentage);
                return $report;
            });

        if ($quizReport->count()) {
            $groupedObjects = [];

            foreach ($quizReport['data'] as $object) {
                $gameId = $object['game_id'];

                if (!isset($groupedObjects[$gameId])) {
                    $groupedObjects[$gameId] = [];
                }

                $groupedObjects[$gameId][] = $object;
            }
            $itemKeys = $groupedObjects;
            $highest_scores = [];


            foreach ($itemKeys as $key => $group) {
                $totalScores = array_column($group, 'total_score');
                $totalScores = array_map('intval', $totalScores);

                $highest_score = max($totalScores);
                $highest_scores[$key] = $highest_score;
            }

            $quizReport['highest_score'] = $highest_scores;
        }
    }
}
