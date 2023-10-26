<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\PassingPercentage;
use App\Models\PerfectScore;
use App\Models\PivotRetakes;
use App\Models\Retake;
use App\Models\SkillTest;
use App\Models\Student;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkillTestController extends Controller
{
    public function addSkillTest(Request $request)
    {
        $student_id = Student::where('user_id', Auth::user()->id)->first()->id;
        $skillTest = SkillTest::where('student_id', $student_id)
            ->where('letter', $request->letter)
            ->count();

        $expiresAt = new DateTime();
        $expiresAt->modify('+1 year');
        $image = $request->file('video'); //image file from frontend
        $firebase_storage_path = 'skill-test/videos/';
        $name = 'ST' . rand(100000, 999999);
        $localfolder = public_path('firebase-temp-uploads') . '/';
        $extension = $image->getClientOriginalExtension();
        $file      = $name . '.' . $extension;
        if ($image->move($localfolder, $file)) {
            $uploadedfile = fopen($localfolder . $file, 'r');
            $path = app('firebase.storage')->getBucket()->upload($uploadedfile, [
                'name' => $firebase_storage_path . $file
            ])->signedUrl($expiresAt);
            unlink($localfolder . $file);
            if ($path) {
                SkillTest::create([
                    'student_id' => $student_id,
                    'letter' => $request->letter,
                    'flag' => $request->flag,
                    'file_url' => $path,
                    'object' => $request->object,
                    'status' => 'pending'
                ]);
                $retake = Retake::where('student_id', $student_id)
                    ->where('flag', 'skill_test')
                    ->where('textbook_flag', $request->flag)
                    ->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(attributes, "$.alphabet")) = ?', [$request->letter])
                    ->first();

                if ($retake) {
                    if ($retake->allowed_retake > 0) {
                        $retake->update(['allowed_retake' => $retake->allowed_retake - 1]);
                    } else {
                        $retake->update(['allowed_retake' => 0]);
                    }
                } else {
                    Retake::create([
                        'allowed_retake' => 0,
                        'student_id' => $student_id,
                        'flag' => 'skill_test',
                        'textbook_flag' => $request->flag,
                        'attributes' => json_encode((object)[
                            'alphabet' => $request->letter
                        ])
                    ]);
                }

                $pivotRetake = PivotRetakes::where('student_id', $student_id)->first();

                if (!isset($pivotRetake)) {
                    $pivotRetake = new PivotRetakes;
                    $pivotRetake->student_id = $student_id;
                    $pivotRetake->main_retake = 0;
                    $pivotRetake->textbook_flag = $request->flag;
                    $pivotRetake->save();
                }

                Notification::create([
                    'student_id' => $student_id,
                    'flag' => $skillTest != 0 ? 'fs_resubmit_skill_test' : 'fs_first_skill_test',
                    'subject' => 'for_teacher',
                    'url' => '/student-quiz-report' . '/' . $student_id,
                    'status' => 'unread',
                    'attributes' => json_encode((object)[
                        'alphabet' => $request->letter,
                        'textbook_name' => ucwords(str_replace('-', ' ', $request->flag))
                    ])
                ]);
                return response()->json([
                    'message' => 'Skill test is uploaded successfull.'
                ]);
            }
            return response()->json([
                'message' => 'Something went wrong.'
            ], 400);
        }
        return response()->json([
            'message' => 'Something went wrong.'
        ]);
    }

    public function getSkillTest(Request $request, $flag)
    {
        $retakes = [];
        $skillTests = [];
        $highestScores = [];

        if (!$request->get('student_id')) {
            $studentId = Student::where('user_id', Auth::user()->id)->first()->id;
        } else {
            $studentId = $request->get('student_id');
        }

        $skillTests = SkillTest::where('student_id', $studentId)
            ->where('flag', $flag)
            ->get()
            ->map(function ($skill_test) use ($studentId) {
                $perfect_score = PerfectScore::where('student_id', $studentId)->first()->score ?? 10;
                $score = (int)$skill_test->score;
                $percentage = ((int)$skill_test->score / $perfect_score) * 100;
                $passing_score = $perfect_score * .75;

                $skill_test->perfect_score = $perfect_score;
                $skill_test->percentage = $percentage;
                $skill_test->mark = $score > round($passing_score) ? 'Passed' : 'Failed';

                return $skill_test;
            });


        if (count($skillTests)) {
            $itemKeys = $skillTests->groupBy('letter');

            $highestScores = [];
            $itemKeys->each(function ($group, $letter) use (&$highestScores) {
                $highestScore = $group->max('score');
                $highestScores[$letter] = $highestScore;
            });

            $retakes = Retake::where('student_id', $studentId)
                ->where('flag', 'skill_test')
                ->where('textbook_flag', $flag)
                ->get()
                ->map(function ($data) {
                    $data->attributes = json_decode($data->attributes);
                    return $data;
                })
                ->groupBy(function ($item) {
                    return $item->attributes->alphabet;
                })
                ->map(function ($group) {
                    return $group->first();
                });
        }

        return (object)[
            'data' => $skillTests,
            'retake' => $retakes,
            'highest_score' => $highestScores
        ];
    }

    public function getAllSkillTests()
    {
        $skillTest = [];
        $student = Student::where('user_id', Auth::user()->id)->first();

        if ($student) {
            $studentId = $student->id;
            $teacherId = $student->teacher_id;
            $passingPercentage = PassingPercentage::where('teacher_id', $teacherId)->first()->percentage;
            $skillTests = SkillTest::where('student_id', $studentId)->get();

            if ($skillTests->isNotEmpty()) {
                $itemKeys = $skillTests->groupBy('flag');

                $itemKeys->each(function ($group, $letter) use ($passingPercentage, &$skillTest) {
                    $alphabetGroup = $group->groupBy('letter');
                    $average = $group->avg('score');
                    $submitted = $alphabetGroup->count();
                    $percentage = ($average / 10) * 100;
                    $mark = ($percentage >= $passingPercentage) ? 'Passed' : 'Failed';

                    $skillTest['data'][$letter] = [
                        'average' => $average,
                        'submitted' => $submitted,
                        'percentage' => $percentage,
                        'passing_percentage' => $passingPercentage,
                        'mark' => $mark,
                    ];
                });
            }
        }

        return $skillTest;
    }

    public function modifyExistingObject($flag, $object)
    {
        $studentId = Student::where('user_id', Auth::user()->id)->first()->id;
        $data = SkillTest::where('student_id', $studentId)
            ->where('flag', $flag)
            ->where('object', $object)
            ->get();
        if (count($data) > 0) {
            SkillTest::where('student_id', $studentId)
                ->where('flag', $flag)
                ->where('object', $object)
                ->delete();
        }

        return true;
    }

    public function updateSkillTest(Request $request, $id)
    {
        $test = SkillTest::find($id);
        $test->update([
            'status' => $request->get('status')
        ]);

        return response()->json([
            'message' => 'Skill test checked.'
        ]);
    }

    public function updateScore(Request $request)
    {
        $skill_test = SkillTest::find($request->skill_test_id);
        $skill_test->score = $request->score;
        $skill_test->save();

        return response()->json([
            'message' => 'Score is updated.'
        ]);
    }

    public function updateMark(Request $request)
    {
        $skillTest = SkillTest::find($request->skill_test_id);
        if ($request->input('mark') === 'correct') {
            $skillTest->score = 10;
        } else {
            $skillTest->score = 5;
        }
        $skillTest->status = $request->input('mark');
        $skillTest->save();

        return response()->json([
            'message' => 'Skill test mark and score is updated successfully.'
        ]);
    }

    public function allowRetake(Request $request, $studentId)
    {
        $alphabets = $request->input('alphabets');
        $retakes = Retake::where('student_id', $studentId)
            ->where('flag', 'skill_test')
            ->where('textbook_flag', $request->query('flag'))
            ->whereIn('attributes->alphabet', $alphabets);

        $retakes->update(['allowed_retake' => $request->input('allowed_retake')]);
        $newRetakes = $retakes->first();

        $url = null;
        switch ($newRetakes->textbook_flag) {
            case 'alphabet-words':
                $url = '/alphabet-by-words';
                break;

            case 'alphabet-letters':
                $url = '/alphabet-by-letters';
                break;

            case 'vowel-consonants':
                $url = '/vowel-consonants';
                break;
        }

        Notification::create([
            'student_id' => $newRetakes->student_id,
            'flag' => 'ft_retake_skill_test',
            'subject' => 'for_student',
            'url' => $url,
            'status' => 'unread',
            'attributes' => json_encode((object)[
                'alphabet' => json_decode($newRetakes->attributes)->alphabet,
                'textbook_name' => ucwords(str_replace('-', ' ', $newRetakes->textbook_flag))
            ])
        ]);
        return response()->json([
            'message' => 'Allowed retake successfully updated.'
        ]);
    }
}
