<?php

namespace App\Http\Controllers;

use App\Models\QuantityRequirement;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuantityRequirementController extends Controller
{
    public function index(Request $request)
    {
        $teacherId = null;
        $teacher = Teacher::where('user_id', Auth::user()->id)->first();
        if ($teacher) {
            $teacherId = $teacher->id;
        } else {
            $student = Student::where('user_id', Auth::user()->id)->first();
            $teacherId = $student->teacher_id;
        }
        return QuantityRequirement::where('teacher_id', $teacherId)->get();
    }

    public function updateQuantityRequirement(Request $request, $id)
    {
        $quantityRequirement = QuantityRequirement::find($id);
        $quantityRequirement->value = $request->input('value');
        $quantityRequirement->save();

        return 'Quantity requirement has been successfully updated.';
    }
}
