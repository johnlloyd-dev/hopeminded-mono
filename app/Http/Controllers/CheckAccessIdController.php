<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class CheckAccessIdController extends Controller
{
    public function checkAccessId(Request $request) {
        $student = Student::whereNotNull('access_id')->where('access_id', 'HM-'.$request->get('accessId'))->first();
        if(count((array)$student)) {
            if($student->is_registered == 0) {
                return $student;
            } else {
                return response()->json([
                    'errors' => [
                        'access_id' => ["This student's access id is already registered."]
                    ]
                ], 404);
            }
        } else {
            return response()->json([
                'errors' => [
                    'access_id' => ['Access ID not found.']
                ]
            ], 404);
        }
    }
}
