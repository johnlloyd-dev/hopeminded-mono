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
                $student->skill_test = SkillTest::where('student_id', $student->id)
                    ->orderBy('letter')
                    ->distinct('letter')
                    ->get();
                $quiz_reports = QuizReport::where('student_id', $student->id);
                // Memory Game
                $memory_game = $quiz_reports->where('game_id', 3)->avg('total_score');
                $memory_game_perfect_score = Game::where('id', 3)->first()->perfect_score;
                $student->memory_game = ($memory_game ?? 0) . '/' . $memory_game_perfect_score;
                // Typing Balloon
                $typing_balloon = $quiz_reports->where('game_id', 2)->avg('total_score');
                $typing_balloon_perfect_score = Game::where('id', 2)->first()->perfect_score;
                $student->typing_balloon = ($typing_balloon ?? 0) . '/' . $typing_balloon_perfect_score;
                // Hangman Game
                $hangman_game = $quiz_reports->where('game_id', 1)->avg('total_score');
                $hangman_game_perfect_score = Game::where('id', 1)->first()->perfect_score;
                $student->hangman_game = ($hangman_game  ?? 0) . '/' . $hangman_game_perfect_score;

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
