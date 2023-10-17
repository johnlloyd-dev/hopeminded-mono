<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\QuizMistake;
use App\Models\QuizReport;
use App\Models\Retake;
use App\Models\SkillTest;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function getStatistics(Request $request)
    {
        $quizMistakes = QuizMistake::select('quiz_mistakes.*')
            ->where('game_flag', $request->query('game_flag'))
            ->join('students', function ($query) {
                $query->on('students.id', '=', 'quiz_mistakes.student_id')
                    ->where('students.teacher_id', Teacher::where('user_id', Auth::user()->id)->first()->id);
            })
            ->get()
            ->map(function ($data) {
                $data->attributes = json_decode($data->attributes);

                return $data;
            });

        $skillTestMistakes = SkillTest::select('skill_tests.*')
            ->where('flag', $request->query('textbook_flag'))
            ->join('students', function ($query) {
                $query->on('students.id', '=', 'skill_tests.student_id')
                    ->where('students.teacher_id', Teacher::where('user_id', Auth::user()->id)->first()->id);
            })
            ->get();

        // Initialize an empty result array to store the grouped data
        $result = [];

        foreach ($quizMistakes as $item) {
            $gameFlag = $item->game_flag;
            $alphabet = $gameFlag !== 'typing-balloon' ? $item->attributes->alphabet : strtoupper($item->attributes->answer);
            $mark = $item->attributes->mark;

            // Check if the alphabet exists in the result array
            if (!isset($result[$alphabet])) {
                $result[$alphabet] = [
                    'correct' => 0,
                    'wrong' => 0,
                ];
            }

            // Update the counts based on the mark
            if ($mark === 'correct') {
                $result[$alphabet]['correct']++;
            } elseif ($mark === 'wrong') {
                $result[$alphabet]['wrong']++;
            }
        }

        // Initialize an empty result array to store the grouped data
        $result2 = [];

        foreach ($skillTestMistakes as $item) {
            $alphabet = strtoupper($item->letter);
            $mark = $item->status;

            // Check if the alphabet exists in the result array
            if (!isset($result2[$alphabet])) {
                $result2[$alphabet] = [
                    'correct' => 0,
                    'wrong' => 0,
                    'pending' => 0
                ];
            }

            // Update the counts based on the mark
            if ($mark === 'correct') {
                $result2[$alphabet]['correct']++;
            } elseif ($mark === 'wrong') {
                $result2[$alphabet]['wrong']++;
            } elseif ($mark === 'pending') {
                $result2[$alphabet]['pending']++;
            }
        }

        return [
            'quiz' => $result,
            'skill_test' => $result2
        ];

        return $result;
    }

    public function getStatisticsSummary(Request $request)
    {
        $quizMistakes = QuizMistake::select('quiz_mistakes.*', 'students.id as student_id', DB::raw('CONCAT(students.first_name, " ", students.middle_name, " ", students.last_name) as full_name'))
            ->join('students', function ($query) {
                $query->on('students.id', '=', 'quiz_mistakes.student_id')
                    ->where('students.teacher_id', Teacher::where('user_id', Auth::user()->id)->first()->id);
            })
            ->where('game_flag', $request->query('game_flag'))
            ->when($request->query('game_flag') === 'typing-balloon', function ($query) use ($request) {
                $query->whereJsonContains('attributes', ['answer' => strtolower($request->query('alphabet'))]);
            })
            ->when($request->query('game_flag') !== 'typing-balloon', function ($query) use ($request) {
                $query->whereJsonContains('attributes', ['alphabet' => $request->query('alphabet')]);
            })
            ->get()
            ->map(function ($data) {
                $data->attributes = json_decode($data->attributes);

                return $data;
            });

        $skillTestMistakes = SkillTest::select('skill_tests.*', 'students.id as student_id', DB::raw('CONCAT(students.first_name, " ", students.middle_name, " ", students.last_name) as full_name'))
            ->join('students', function ($query) {
                $query->on('students.id', '=', 'skill_tests.student_id')
                    ->where('students.teacher_id', Teacher::where('user_id', Auth::user()->id)->first()->id);
            })
            ->where('flag', $request->query('textbook_flag'))
            ->where('letter', strtolower($request->query('alphabet')))
            ->get()
            ->map(function ($data) {
                $data->alphabet = strtoupper($data->letter);

                return $data;
            });

        $grouped = $quizMistakes->groupBy('full_name');

        $grouped2 = $skillTestMistakes->groupBy('full_name');

        $summary = $grouped->map(function ($group) {
            return [
                'student_id' => $group->first()->student_id,
                'full_name' => $group->first()->full_name,
                'wrong_count' => $group->where('attributes.mark', 'wrong')->count(),
                'correct_count' => $group->where('attributes.mark', 'correct')->count(),
            ];
        });

        $summary2 = $grouped2->map(function ($group) {
            return [
                'student_id' => $group->first()->student_id,
                'full_name' => $group->first()->full_name,
                'wrong_count' => $group->where('status', 'wrong')->count(),
                'correct_count' => $group->where('status', 'correct')->count(),
                'pending_count' => $group->where('status', 'pending')->count(),
            ];
        });

        return [
            'quiz' => $summary,
            'skill_test' => $summary2
        ];
    }
}
