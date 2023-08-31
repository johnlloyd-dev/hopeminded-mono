<?php

namespace App\Http\Controllers;

use App\Models\PerfectScore;
use App\Models\SkillTest;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerfectScoreController extends Controller
{
    public function getPerfectScore(Request $request)
    {
        $perfect_score = PerfectScore::where('student_id', $request->query('student_id'))->first();
        if ($perfect_score)
            return $perfect_score;
        else {
            return (object)[
                'id' => null,
                'score' => 10
            ];
        }
    }

    public function modifyPerfectScore(Request $request)
    {
        $maxScore = SkillTest::where('student_id', $request->query('student_id'))->select('score')->max('score');
        if ($request->input('score') < $maxScore) {
            return response()->json([
                'error' => true
            ], 400);
        }
        if (!$request->perfect_score_id) {
            PerfectScore::create([
                'flag' => $request->flag,
                'score' => $request->score,
                'student_id' => $request->query('student_id')
            ]);
            return response()->json([
                'message' => 'Perfect score successfully added.'
            ]);
        } else {
            PerfectScore::find($request->perfect_score_id)->update(['score' => $request->score]);
            return response()->json([
                'message' => 'Perfect score successfully updated.'
            ]);
        }
    }
}
