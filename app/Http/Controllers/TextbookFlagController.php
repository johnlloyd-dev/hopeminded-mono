<?php

namespace App\Http\Controllers;

use App\Models\FlagAttribute;
use App\Models\Student;
use App\Models\TextbookFlag;
use Illuminate\Http\Request;

class TextbookFlagController extends Controller
{
    public function getFlags(Request $request, $flag) {
        $student = Student::where('user_id', $request->user()->id)->first();
        return FlagAttribute::where('student_id', $student->id)->where('flag', $flag)->first();
    }

    public function updateFlag(Request $request, $flag) {
        $student = Student::where('user_id', $request->user()->id)->first();
        $model = FlagAttribute::where('student_id', $student->id)->where('flag', $flag)->first();
        $jsonData = json_decode($model->attributes, true);
        
        if($flag != 'alphabet-words') {
            $jsonData[$request->letter] = true;
            FlagAttribute::where('student_id', $student->id)->where('flag', $flag)->update([
                "attributes" => $jsonData
            ]);
        } else {
            $jsonData[$request->letter][] = $request->index;
            FlagAttribute::where('student_id', $student->id)->where('flag', $flag)->update([
                "attributes" => $jsonData
            ]);
        }

        return 'Flag is updated successfully.';
    }
}
