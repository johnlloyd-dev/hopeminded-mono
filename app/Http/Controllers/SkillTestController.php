<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\PerfectScore;
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

                Retake::create([
                    'allowed_retake' => 0,
                    'student_id' => $student_id,
                    'flag' => 'skill_test',
                    'textbook_flag' => $request->flag,
                    'attributes' => json_encode((object)[
                        'alphabet' => $request->letter
                    ])
                ]);

                Notification::create([
                    'student_id' => $student_id,
                    'flag' => $skillTest != 0 ? 'fs_resubmit_skill_test' : 'fs_first_skill_test',
                    'subject' => 'for_teacher',
                    'url' => '/student-quiz-report' . '/' . $student_id,
                    'status' => 'unread',
                    'attributes' => json_encode((object)[
                        'alphabet' => $request->letter
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

    public function getSkillTest($studentId, $flag)
    {
        if ($studentId == null || $studentId == 'null') {
            $studentId = Student::where('user_id', Auth::user()->id)->first()->id;
        }
        $skillTest['data'] = SkillTest::where('student_id', $studentId)
            ->where('flag', $flag)
            ->get()
            ->map(function ($skill_test) use ($studentId) {
                $teacher_id = Student::where('id', $studentId)->first()->teacher_id;
                $perfect_score = PerfectScore::where('teacher_id', $teacher_id)->first()->score ?? 10;
                $score = (int)$skill_test->score;
                $percentage = ((int)$skill_test->score / $perfect_score) * 100;
                $passing_score = $perfect_score * .75;

                $skill_test->perfect_score = $perfect_score;
                $skill_test->percentage = $percentage;
                $skill_test->mark = $score > round($passing_score) ? 'Passed' : 'Failed';
                return $skill_test;
            })
            ->toArray();


        if (count($skillTest['data'])) {
            $groupedObjects = [];

            foreach ($skillTest['data'] as $object) {
                $letter = $object['letter'];

                if (!isset($groupedObjects[$letter])) {
                    $groupedObjects[$letter] = [];
                }

                $groupedObjects[$letter][] = $object;
            }
            $average = $groupedObjects;
            $averages = [];

            foreach ($average as $key => $group) {
                $totalScores = array_column($group, 'score');
                $totalScores = array_map('intval', $totalScores);

                $average = array_sum($totalScores) / count($totalScores);
                $averages[$key] = $average;
            }

            $skillTest['average'] = $averages;

            $retake = Retake::where('student_id', $studentId)
                ->where('flag', 'skill_test')
                ->get()
                ->map(function ($data) {
                    $data->attributes = json_decode($data->attributes);
                    return $data;
                })
                ->toArray();

            $groupedRetakes = [];
            foreach ($retake as $data) {
                if (isset($data['attributes']->alphabet)) {
                    $alphabet = $data['attributes']->alphabet;
                    if (!isset($groupedRetakes[$alphabet])) {
                        $groupedRetakes[$alphabet] = [];
                    }
                    $groupedRetakes[$alphabet] = $data;
                }
            }

            $skillTest['retake'] = $groupedRetakes;
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

    public function allowRetake(Request $request, $retakeId)
    {
        $retake = Retake::find($retakeId);

        $retake->allowed_retake = $request->input('allowed_retake');
        $retake->save();

        $url = null;
        switch ($retake->textbook_flag) {
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
            'student_id' => $retake->student_id,
            'flag' => 'ft_retake_skill_test',
            'subject' => 'for_student',
            'url' => $url,
            'status' => 'unread',
            'attributes' => json_encode((object)[
                'alphabet' => json_decode($retake->attributes)->alphabet
            ])
        ]);
        return response()->json([
            'message' => 'Allowed retake successfully updated.'
        ]);
    }
}
