<?php

namespace App\Http\Controllers;

use App\Models\PerfectScore;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerfectScoreController extends Controller
{
    public function getPerfectScore(Request $request)
    {
        $teacher_id = Teacher::where('user_id', Auth::user()->id)->first()->id;
        $perfect_score = PerfectScore::where('teacher_id', $teacher_id)->first();
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
        if (!$request->perfect_score_id) {
            PerfectScore::create([
                'flag' => $request->flag,
                'score' => $request->score,
                'teacher_id' => Teacher::where('user_id', Auth::user()->id)->first()->id
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
