<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentRequest;
use App\Models\Game;
use App\Models\QuizReport;
use App\Models\SkillTest;
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
        $teacherId = Teacher::where('user_id', Auth::user()->id)->first()->id;
        return User::join('students', function (JoinClause $join) {
            $join->on('users.id', '=', 'students.user_id');
        })
            ->select('users.*', 'students.*')
            ->where('students.teacher_id', $teacherId)
            ->get()
            ->map(function ($student) {
                $skillTests = SkillTest::where('student_id', $student->id)
                    ->orderBy('letter')
                    ->distinct('letter')
                    ->get();

                $groupedObjects = [];

                foreach ($skillTests as $object) {
                    $flag = $object['flag'];

                    if (!isset($groupedObjects[$flag])) {
                        $groupedObjects[$flag] = [];
                    }

                    $groupedObjects[$flag][] = $object;
                }
                $itemKeys = $groupedObjects;
                $highest_scores = [];

                foreach ($itemKeys as $key => $group) {
                    $groupedObjects2 = [];

                    foreach ($group as $object2) {
                        $flag2 = $object2['letter'];

                        if (!isset($groupedObjects2[$flag2])) {
                            $groupedObjects2[$flag2] = [];
                        }

                        $groupedObjects2[$flag2][] = $object2;
                    }
                    $itemKeys2 = $groupedObjects2;

                    foreach ($itemKeys2 as $key2 => $group2) {
                        $totalScores = array_column($group2, 'score');
                        $totalScores = array_map('intval', $totalScores);

                        $highest_score = max($totalScores);
                        $highest_scores[$key][$key2] = $highest_score;
                    }
                }

                $student->skill_test = $highest_scores;
                // Memory Game
                $memory_game = QuizReport::where('student_id', $student->id)->where('flag', 'quiz')->where('game_id', 3)->select('total_score')->max('total_score');
                $memory_game_perfect_score = Game::where('id', 3)->first()->perfect_score;
                $student->memory_game = ($memory_game ?? 0) . '/' . $memory_game_perfect_score;
                // Typing Balloon
                $typing_balloon = QuizReport::where('student_id', $student->id)->where('flag', 'quiz')->where('game_id', 2)->select('total_score')->max('total_score');
                $typing_balloon_perfect_score = Game::where('id', 2)->first()->perfect_score;
                $student->typing_balloon = ($typing_balloon ?? 0) . '/' . $typing_balloon_perfect_score;
                // Hangman Game
                $hangman_game = QuizReport::where('student_id', $student->id)->where('flag', 'quiz')->where('game_id', 1)->select('total_score')->max('total_score');
                $hangman_game_perfect_score = Game::where('id', 1)->first()->perfect_score;
                $student->hangman_game = ($hangman_game ?? 0) . '/' . $hangman_game_perfect_score;

                return $student;
            })
            ->toArray();
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
