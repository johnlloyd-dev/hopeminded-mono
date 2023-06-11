<?php

namespace App\Http\Controllers;

use App\Models\QuizReport;
use Illuminate\Http\Request;

class QuizReportController extends Controller
{
    public function getStudentQuizReport($studentId) {
        return QuizReport::where('student_id', $studentId)
            ->where('flag', 'quiz')
            ->orderByRaw('-total_score ASC')
            ->get();
    }
}
