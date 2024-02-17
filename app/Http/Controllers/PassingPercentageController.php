<?php

namespace App\Http\Controllers;

use App\Models\PassingPercentage;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PassingPercentageController extends Controller
{
    public function index()
    {
        $teacherId = null;
        $teacher = Teacher::where('user_id', Auth::user()->id)->first();

        if ($teacher) {
            $teacherId = $teacher->id;
        } else {
            $teacherId = Student::where('user_id', Auth::user()->id)->first()->teacher_id;
        }

        $data = $this->getPassingPercentage($teacherId);
        $newData = [];
        foreach ($data as $item) {
            $newData[$item['flag']] = $item;
        }
        return $newData;
    }

    public function getAllPassingPercentage()
    {
        $teacherId = Student::where('user_id', Auth::user()->id)->first()->teacher_id;

        return PassingPercentage::where('teacher_id', $teacherId)->get();
    }

    public function getPassingPercentage($teacherId)
    {
        return PassingPercentage::where('teacher_id', $teacherId)->get();
    }

    public function updatePassingPercentage(Request $request, $id)
    {
        $passingPercentage = PassingPercentage::find($id);
        $passingPercentage->percentage = $request->input('percentage');
        $passingPercentage->save();

        return 'Percentage has been successfully updated.';
    }
}
