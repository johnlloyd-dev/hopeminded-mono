<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\QuizReport;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    public function getGameInfo(Request $request)
    {
        $student = Student::where('user_id', Auth::user()->id)->first();
        if ($request->get('flag') && $request->get('flag') === 'tutorial') {
            return QuizReport::where('student_id', $student->id)
                ->where('flag', 'tutorial')
                ->latest('created_at')
                ->first();
        } else {
            return QuizReport::where('student_id', $student->id)
                ->where('flag', 'quiz')
                ->latest('created_at')
                ->first();
        }
    }

    public function allGameRecords(Request $request)
    {
        $student = Student::where('user_id', Auth::user()->id)->first();
        if ($request->get('flag') && $request->get('flag') === 'tutorial') {
            return QuizReport::where('student_id', $student->id)
                ->where('flag', 'tutorial')
                ->where('game_id', $request->gameId)
                ->get();
        } else {
            return QuizReport::where('student_id', $student->id)
                ->where('flag', 'quiz')
                ->where('game_id', $request->gameId)
                ->get();
        }
    }

    public function storeGameInfo(Request $request, $flag)
    {
        $student = Student::where('user_id', Auth::user()->id)->first();
        $gameInfo = Game::where('flag', $flag)->first();
        $quizReport = QuizReport::where('student_id', $student->id)
            ->where('game_id', $gameInfo->id)
            ->latest()
            ->first();
        $attemptNumber = $quizReport ? $quizReport->attempt_number : 1;
        $quiz = QuizReport::create([
            'student_id' => $student->id,
            'game_id' => $gameInfo->id,
            'highest_level' =>  $request->highestLevel,
            'total_score' => $request->score,
            'mark' => 'failed',
            'attempt_number' => $attemptNumber,
            'flag' => $request->get('flag') && $request->get('flag') === 'tutorial' ? 'tutorial' : 'quiz'
        ]);

        return $quiz;
    }

    public function updateGameInfo(Request $request, $id)
    {
        $quiz = QuizReport::findOrFail($id);
        $mark = null;

        $hangmanPassingScore = $request->get('flag') && $request->get('flag') == 'tutorial' ? 6 : 10;
        $balloonPassingScore = $request->get('flag') && $request->get('flag') == 'tutorial' ? 20 : 38;
        $memoryPassingScore = $request->get('flag') && $request->get('flag') == 'tutorial' ? 9 : 44;

        $hangmanPerfectScore = $request->get('flag') && $request->get('flag') == 'tutorial' ? 8 : 18;
        $balloonPerfectScore = $request->get('flag') && $request->get('flag') == 'tutorial' ? 26 : 51;
        $memoryPerfectScore = $request->get('flag') && $request->get('flag') == 'tutorial' ? 12 : 59;

        if ($request->get('gameId') == 1) {
            if ($request->score < $hangmanPassingScore) {
                $mark = 'failed';
            } else {
                $mark = 'passed';
            }
        } else if ($request->get('gameId') == 2) {
            if ($request->score < $balloonPassingScore) {
                $mark = 'failed';
            } else {
                $mark = 'passed';
            }
        } else {
            if ($request->score < $memoryPassingScore) {
                $mark = 'failed';
            } else {
                $mark = 'passed';
            }
        }
        $quiz->update([
            'highest_level' => $request->highestLevel,
            'total_score' => $request->score,
            'mark' => $mark
        ]);

        return $quiz;
    }

    public function getQuizReports(Request $request)
    {
        $student = Student::where('user_id', Auth::user()->id)->first();
        return QuizReport::where('student_id', $student->id)
            ->where('flag', 'quiz')
            ->orderByRaw('-total_score ASC')
            ->get();
    }
}
