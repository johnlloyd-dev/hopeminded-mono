<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\QuizReport;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    public function getGameInfo() {
        $student = Student::where('user_id', Auth::user()->id)->first();
        return QuizReport::where('student_id', $student->id)->latest('created_at')->first();
    }

    public function allGameRecords(Request $request) {
        $student = Student::where('user_id', Auth::user()->id)->first();
        return QuizReport::where('student_id', $student->id)->where('game_id', $request->gameId)->get();
    }

    public function storeGameInfo(Request $request, $flag) {
        $student = Student::where('user_id', Auth::user()->id)->first();
        $gameInfo = Game::where('flag', $flag)->first();
        $quiz = QuizReport::create([
            'student_id' => $student->id,
            'game_id' => $gameInfo->id,
            'highest_level' =>  $request->highestLevel,
            'total_score' => $request->score,
            'mark' => 'failed'
        ]);

        return $quiz;
    }

    public function updateGameInfo(Request $request, $id) {
        $quiz = QuizReport::findOrFail($id);
        $mark = null;
        if($request->gameId == 1) {
            if ($request->score < 10) {
                $mark = 'failed';
            } else {
                $mark = 'passed';
            }
        } else if($request->gameId == 2) {
            if ($request->score < 38) {
                $mark = 'failed';
            } else {
                $mark = 'passed';
            }
        } else {
            if ($request->score < 44) {
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

    public function getQuizReports(Request $request) {
        $student = Student::where('user_id', Auth::user()->id)->first();
        return QuizReport::where('student_id', $student->id)->get();
    }
}
