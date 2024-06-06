<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Game;
use App\Models\PassingPercentage;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    public function getTopStudents()
    {
        $teacher_id = Teacher::where('user_id', Auth::user()->id)->first()->id;
        $games = Game::with('quizScore')->get();

        $passing_percentages = PassingPercentage::where('teacher_id', $teacher_id)->get();

        return Student::select(
            DB::raw('CONCAT(students.first_name, " ", students.middle_name, " ", students.last_name) as full_name'),
            'students.id'
        )
            ->with(['quiz', 'skill_test'])
            ->where('teacher_id', $teacher_id)
            ->orderByRaw('id')
            ->get()
            ->filter(function ($report) {
                $quiz = $report->quiz->first();
                $skillTest = $report->skill_test->first();

                $quizCount = $report->quiz->count();
                $skillTestTextbookCount = $report->skill_test->groupBy('flag')->count();
                $skillTestComplete = $report->skill_test->groupBy('flag')->every(function ($data, $key) {
                    if ($key == 'numbers') {
                        return $data->count() >= 10;
                    } else {
                        return $data->count() >= 26;
                    }
                });

                if ($quiz && $skillTest) {
                    // if ($quizCount >= 4) {
                    if ($quizCount >= 4 && ($skillTestTextbookCount >= 4 && $skillTestComplete)) {
                        return TRUE;
                    }
                }

                return FALSE;
            })
            ->values()
            ->map(function ($data) use ($games, $passing_percentages) {
                // Quiz
                $data->quiz->transform(function ($data) use ($games) {
                    $game = $games->firstWhere('id', $data->game_id);
                    $perfectScore = $game->quizScore->first()->perfect_score;

                    $data->perfect_score = $perfectScore;
                    $data->percentage = round(($data->highest_score / $perfectScore) * 100);

                    return $data;
                });
                // End of Quiz

                // Skill Test
                $skillTest = $data->skill_test->groupBy('flag')
                    ->values()
                    ->map(function ($data) {
                        $item = $data->first();
                        $perfectScore = 0;

                        if ($item->flag != 'numbers') {
                            $perfectScore = 260;
                        } else {
                            $perfectScore = 100;
                        }

                        $totalScore = $data->sum('highest_score');

                        $percentage = round(($totalScore / $perfectScore) * 100);

                        return [
                            'total' => $totalScore,
                            'perfect_score' => $perfectScore,
                            'percentage' => $percentage,
                            'flag' => $item['flag'],
                            'student_id' => $item['student_id']
                        ];
                    });
                // End of Skill Test

                $data->quiz_percentage = round($data->quiz->sum('percentage') / $data->quiz->count());
                $data->quiz_mark = $data->quiz_percentage < $passing_percentages->firstWhere('flag', 'quiz')->percentage ? 'failed' : 'passed';

                $data->skill_test_percentage = round($skillTest->sum('percentage') / $skillTest->count());
                $data->skill_test_mark = $data->skill_test_percentage < $passing_percentages->firstWhere('flag', 'skill_test')->percentage ? 'failed' : 'passed';

                $data->overall_percentage = ($data->skill_test_percentage + $data->quiz_percentage) / 2;
                $data->overall_mark = $data->overall_percentage < $passing_percentages->firstWhere('flag', 'overall')->percentage ? 'failed' : 'passed';

                unset($data->quiz);
                unset($data->skill_test);

                return $data;
            })
            ->take(10)
            ->sortByDesc('overall_percentage')
            ->values();
    }
}
