<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\QuizReport;
use App\Models\Retake;
use Illuminate\Http\Request;

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
            $averages = [];


            foreach ($itemKeys as $key => $group) {
                $totalScores = array_column($group, 'total_score');
                $totalScores = array_map('intval', $totalScores);

                $average = array_sum($totalScores) / count($totalScores);
                $averages[$key] = $average;
            }

            $quizReport['average'] = $averages;

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
                if (isset($data['attributes']->game_id)) {
                    $gameId = $data['attributes']->game_id;
                    if (!isset($groupedRetakes[$gameId])) {
                        $groupedRetakes[$gameId] = [];
                    }
                    $groupedRetakes[$gameId] = $data;
                }
            }

            $quizReport['retake'] = $groupedRetakes;
        }

        return $quizReport;
    }
}
