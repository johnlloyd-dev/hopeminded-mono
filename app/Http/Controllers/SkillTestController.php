<?php

namespace App\Http\Controllers;

use App\Models\SkillTest;
use App\Models\Student;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkillTestController extends Controller
{
    public function addSkillTest(Request $request) {
        $expiresAt = new DateTime();
        $expiresAt->modify('+1 year');
        $image = $request->file('video'); //image file from frontend
        $firebase_storage_path = 'skill-test/videos/';
        $name = 'ST' . rand(100000, 999999);
        $localfolder = public_path('firebase-temp-uploads') .'/';
        $extension = $image->getClientOriginalExtension();
        $file      = $name. '.' . $extension;
        if ($image->move($localfolder, $file)) {
            $uploadedfile = fopen($localfolder.$file, 'r');
            $path = app('firebase.storage')->getBucket()->upload($uploadedfile, [
                'name' => $firebase_storage_path . $file
            ])->signedUrl($expiresAt);
            unlink($localfolder . $file);
            if($path) {
                $modify = self::modifyExistingObject($request->flag, $request->object);
                if($modify) {
                    SkillTest::create([
                        'student_id' => Student::where('user_id', Auth::user()->id)->first()->id,
                        'letter' => $request->letter,
                        'flag' => $request->flag,
                        'file_url' => $path,
                        'object' => $request->object,
                        'status' => 'pending'
                    ]);
                    return response()->json([
                        'message' => 'Skill test is uploaded successfull.'
                    ]);
                }
            }
            return response()->json([
                'message' => 'Something went wrong.'
            ], 400);
        }
        return response()->json([
            'message' => 'Something went wrong.'
        ]);
    }

    public function getSkillTest($studentId, $flag) {
        if($studentId == null || $studentId == 'null') {
            $studentId = Student::where('user_id', Auth::user()->id)->first()->id;
        }
        return SkillTest::where('student_id', $studentId)
            ->where('flag', $flag)
            ->get();
    }

    public function modifyExistingObject($flag, $object) {
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

    public function updateSkillTest(Request $request, $id) {
        $test = SkillTest::find($id);
        $test->update([
            'status' => $request->get('status')
        ]);

        return response()->json([
            'message' => 'Skill test checked.'
        ]);
    }
}
