<?php

namespace App\Http\Controllers;

use App\Http\Requests\SectionRequest;
use App\Models\Section;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectionController extends Controller
{
    public function index()
    {
        $teacherId = Teacher::where('user_id', Auth::user()->id)->first()->id;
        return Section::where('teacher_id', $teacherId)->get();
    }

    public function addSection(SectionRequest $request)
    {
        Section::create([
            'name' => $request->input('name'),
            'teacher_id' => Teacher::where('user_id', Auth::user()->id)->first()->id
        ]);

        return response()->json([
            'message' => "A new section has been added successfully."
        ]);
    }

    public function updateSection(SectionRequest $request, $id)
    {
        $section = Section::find($id);

        $section->name = $request->input('name');
        $section->status = $request->input('status');

        $section->save();

        return response()->json([
            'message' => "A section has been updated successfully."
        ]);
    }

    public function deleteSection($id)
    {
        $section = Section::find($id);

        $section->delete();

        return response()->json([
            'message' => "A section has been deleted successfully."
        ]);
    }

    public function getStudentsOfSection(Request $request)
    {
        $teacherId = Teacher::where('user_id', Auth::user()->id)->first()->id;
        return Student::select(
            'id',
            'first_name',
            'middle_name',
            'last_name',
            'email'
        )
            ->where('teacher_id', $teacherId)
            ->where('section_id', $request->query('sectionId'))
            ->get();
    }

    public function getStudentsNotInSection(Request $request)
    {
        $teacherId = Teacher::where('user_id', Auth::user()->id)->first()->id;
        return Student::select(
            'id',
            'first_name',
            'middle_name',
            'last_name'
        )
            ->where('teacher_id', $teacherId)
            ->where(function ($query) use ($request) {
                $query->where('section_id', '!=', $request->query('sectionId'))
                    ->orWhereNull('section_id');
            })
            ->get();
    }

    public function addStudentsToSection(Request $request)
    {
        $sectionId = (int)$request->input('sectionId');
        $selectedStudents = json_decode($request->input('selectedStudents'));

        Student::whereIn('id', $selectedStudents)
            ->update([
                'section_id' => $sectionId
            ]);

        return response()->json([
            'message' => 'Students added to section successfully.'
        ]);
    }

    public function removeStudentFromSection($studentId)
    {
        Student::where('id', $studentId)
            ->update([
                'section_id' => NULL
            ]);

        return response()->json([
            'message' => 'Student has been removed from the section successfully.'
        ]);
    }
}
