<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\QuizReport;
use Illuminate\Http\Request;

class QuizReportController extends Controller
{
    public function getStudentQuizReport($studentId)
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
            })
            ->toArray();

        if ($quizReport['data']) {
            $total = 0;
            $count = count($quizReport);

            foreach ($quizReport['data'] as $index => $item) {
                $total += $item["total_score"];
            }
            $quizReport["average"] = ceil($total / $count);
        }

        return $quizReport;
    }
}
