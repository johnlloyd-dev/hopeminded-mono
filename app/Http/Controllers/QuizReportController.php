<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\QuizReport;
use App\Models\Retake;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizReportController extends Controller
{
    public function getStudentQuizReport(Request $request, $studentId)
    {
        $quizReport = [];
        $quizReport['data'] = QuizReport::where('student_id', $studentId)
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

        if ($quizReport['data']) {
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

            $retake = Retake::where('student_id', $studentId)
                ->where('flag', 'quiz')
                ->get()
                ->map(function ($data) {
                    $data->attributes = json_decode($data->attributes);
                    return $data;
                })
                ->toArray();

            $groupedRetakes = [];
            foreach ($retake as $data) {
                if (isset($data['textbook_flag'])) {
                    $textbookFlag = strval($data['textbook_flag']);
                    if (!isset($groupedRetakes[$textbookFlag])) {
                        $groupedRetakes[$textbookFlag] = [];
                    }
                    $groupedRetakes[$textbookFlag] = $data;
                }
            }

            $quizReport['retake'] = $groupedRetakes;
        }

        return $quizReport;
    }

    public function getAvailableQuizRetakes()
    {
        $studentId = Student::where('user_id', Auth::user()->id)->first()->id;
        $retake = Retake::where('student_id', $studentId)
            ->where('flag', 'quiz')
            ->get()
            ->map(function ($data) {
                $data->attributes = json_decode($data->attributes);
                return $data;
            })
            ->toArray();

        $groupedRetakes = [];
        foreach ($retake as $data) {
            if (isset($data['textbook_flag'])) {
                $textbookFlag = strval($data['textbook_flag']);
                if (!isset($groupedRetakes[$textbookFlag])) {
                    $groupedRetakes[$textbookFlag] = [];
                }
                $groupedRetakes[$textbookFlag] = $data;
            }
        }

        return $groupedRetakes;
    }
}
