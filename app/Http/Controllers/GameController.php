<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Notification;
use App\Models\PassingPercentage;
use App\Models\QuizMistake;
use App\Models\QuizReport;
use App\Models\Retake;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            $report = QuizReport::where('student_id', $student->id)
                ->where('flag', 'quiz')
                ->where('game_id', $request->gameId)
                ->get();
            $retake = [];
            if ($report->count()) {
                $retake = Retake::where('student_id', $student->id)
                    ->where('flag', 'quiz')
                    ->whereRaw("JSON_EXTRACT(attributes, '$.game_id') = ?", [$request->gameId])
                    // ->whereJsonContains('attributes', ['game_id' => $request->gameId])
                    ->get()
                    ->map(function ($data) {
                        $data->attributes = json_decode($data->attributes);
                        return $data;
                    });
            }
            return [
                'report' => $report,
                'retake' => $retake
            ];
        }
    }

    public function storeGameInfo(Request $request, $flag)
    {
        $student = Student::where('user_id', Auth::user()->id)->first();
        $gameInfo = Game::where('flag', $flag)->first();
        $quizReport = QuizReport::where('student_id', $student->id)
            ->where('game_id', $gameInfo->id)
            ->where('flag', 'quiz')
            ->get();
        $attemptNumber = count($quizReport) != 0 ? $quizReport[0]->attempt_number + 1 : 1;
        $quiz = QuizReport::create([
            'student_id' => $student->id,
            'game_id' => $gameInfo->id,
            'highest_level' =>  $request->highestLevel,
            'total_score' => $request->score,
            'mark' => 'failed',
            'attempt_number' => $attemptNumber,
            'flag' => $request->get('flag') && $request->get('flag') === 'tutorial' ? 'tutorial' : 'quiz'
        ]);

        $textbookName = null;
        if ($gameInfo->textbook_flag === 'hangman-game') {
            $textbookName = 'Alphabets/Words';
        } else if ($gameInfo->textbook_flag === 'typing-balloon') {
            $textbookName = 'Vowels/Consonants';
        } else if ($gameInfo->textbook_flag === 'memory-game') {
            $textbookName = 'Alphabets/Letters';
        } else {
            $textbookName = 'Numbers';
        }

        if (!$request->get('flag')) {
            if (count($quizReport) > 0) {
                $retake = Retake::where('student_id', $student->id)->where('flag', 'quiz')->where('textbook_flag', $gameInfo->textbook_flag)->first();
                if ($retake) {
                    if ($retake->allowed_retake > 0) {
                        $retake->update(['allowed_retake' => $retake->allowed_retake - 1]);
                    } else {
                        $retake->update(['allowed_retake' => 0]);
                    }
                }
            } else {
                Retake::create([
                    'allowed_retake' => 0,
                    'student_id' => $student->id,
                    'flag' => 'quiz',
                    'textbook_flag' => $gameInfo->textbook_flag,
                    'attributes' => json_encode((object)[
                        'game_id' => $gameInfo->id
                    ])
                ]);
            }
        }

        Notification::create([
            'student_id' => $student->id,
            'flag' => count($quizReport) != 0 ? 'fs_retake_quiz' : 'fs_first_take_quiz',
            'subject' => 'for_teacher',
            'url' => '/student-quiz-report' . '/' . $student->id,
            'status' => 'unread',
            'attributes' => json_encode((object)[
                'game_id' => $gameInfo->id,
                'textbook_name' => $textbookName
            ])
        ]);

        return $quiz;
    }

    public function updateGameInfo(Request $request, $id)
    {
        $gameId = $request->query('gameId');
        $quiz = QuizReport::findOrFail($id);
        $game = Game::find($gameId);
        $teacher_id = Student::where('user_id', Auth::user()->id)->first()->teacher_id;
        $passing_percentages = PassingPercentage::where('teacher_id', $teacher_id)->get();

        $mark = null;
        $passingScore = null;

        $percentage = (int)$passing_percentages->firstWhere('flag', 'quiz')->percentage / 100;

        if ($request->has('flag') && $request->query('flag') == 'tutorial') {
            $passingScore = round((int)$game->tutorialScore->first()->perfect_score * (float)$percentage);
        } else {
            $passingScore = round((int)$game->quizScore->first()->perfect_score * (float)$percentage);
        }

        if ($request->input('score') < $passingScore) {
            $mark = 'failed';
        } else {
            $mark = 'passed';
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
        $quizReports = (object)[];
        $student = Student::where('user_id', Auth::user()->id)->first();
        $quizReports->reports = QuizReport::where('student_id', $student->id)
            ->where('flag', 'quiz')
            ->orderByRaw('-total_score ASC')
            ->get();

        $quizReports->highest_scores['memory_game'] = QuizReport::where('student_id', $student->id)
            ->where('flag', 'quiz')
            ->where('game_id', 3)
            ->select('total_score')
            ->max('total_score');
        $quizReports->highest_scores['typing_balloon'] = QuizReport::where('student_id', $student->id)
            ->where('flag', 'quiz')
            ->where('game_id', 2)
            ->select('total_score')
            ->max('total_score');
        $quizReports->highest_scores['hangman_game'] = QuizReport::where('student_id', $student->id)
            ->where('flag', 'quiz')
            ->where('game_id', 1)
            ->select('total_score')
            ->max('total_score');
        $quizReports->highest_scores['matching_game'] = QuizReport::where('student_id', $student->id)
            ->where('flag', 'quiz')
            ->where('game_id', 4)
            ->select('total_score')
            ->max('total_score');

        return $quizReports;
    }

    public function addQuizMistakeRecord(Request $request)
    {
        QuizMistake::create([
            'quiz_report_id' => $request->input('quiz_report_id'),
            'game_flag' => $request->input('flag'),
            'student_id' => Student::where('user_id', Auth::user()->id)->first()->id,
            'attributes' => $request->input('attributes')
        ]);

        return response()->json([
            'message' => 'Stored successfully.'
        ]);
    }

    public function getWeaknessesData(Request $request, $studentId)
    {
        $quizReport['data'] = QuizMistake::where('student_id', $studentId)
            ->where('game_flag', $request->query('game_flag'))
            ->get()
            ->map(function ($data) {
                $data->attributes = json_decode($data->attributes);
                return $data;
            });
        if (count($quizReport['data'])) {
            if (in_array($quizReport['data'][0]->game_flag, ['memory-game', 'typing-balloon'])) {
                $jsonFile = public_path('json/games/balloon-game.json');
                $jsonData = json_decode(file_get_contents($jsonFile), true);
                $quizReport['answer_key'] = $jsonData;
            } else if ($quizReport['data'][0]->game_flag === 'matching-game') {
                $jsonFile = public_path('json/games/matching-game.json');
                $jsonData = json_decode(file_get_contents($jsonFile), true);
                $quizReport['answer_key'] = $jsonData;
            } else {
                $jsonFile = public_path('json/games/hangman-game.json');
                $jsonData = json_decode(file_get_contents($jsonFile), true);
                $quizReport['answer_key'] = $jsonData;
            }
        }

        return $quizReport;
    }

    public function allowRetake(Request $request, $retakeId)
    {
        $retake = Retake::find($retakeId);

        $retake->allowed_retake = $request->input('allowed_retake');
        $retake->save();

        $url = null;
        switch ($retake->textbook_flag) {
            case 'alphabet_words':
                $url = '/alphabet-by-words';
                break;

            case 'alphabet_letters':
                $url = '/alphabet-by-letters';
                break;

            case 'vowel_consonants':
                $url = '/vowel-consonants';
                break;
            default:
                $url = 'numbers';
                break;
        }

        $textbookName = null;
        if ($retake->textbook_flag === 'hangman-game')
            $textbookName = 'Alphabets/Words';
        else if ($retake->textbook_flag === 'typing-balloon')
            $textbookName = 'Vowels/Consonants';
        else if ($retake->textbook_flag === 'memory-game')
            $textbookName = 'Alphabets/Letters';
        else
            $textbookName = 'Numbers';

        Notification::create([
            'student_id' => $retake->student_id,
            'flag' => 'ft_retake_quiz',
            'subject' => 'for_student',
            'url' => $url,
            'status' => 'unread',
            'attributes' => json_encode((object)[
                'game_id' => json_decode($retake->attributes)->game_id,
                'textbook_name' => $textbookName
            ])
        ]);

        return response()->json([
            'message' => 'Retake allowed.'
        ]);
    }
}
