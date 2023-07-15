<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;

class CheckAccessIdController extends Controller
{
    public function checkAccessId(Request $request)
    {
        $teacher = Teacher::whereNotNull('access_id')->where('access_id', 'HM-' . $request->get('accessId'))->first();
        if (count((array)$teacher)) {
            if ($teacher->is_registered == 0) {
                return $teacher;
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
