<?php

namespace App\Http\Controllers;

use App\Http\Requests\SectionRequest;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index()
    {
        return Section::all();
    }

    public function addSection(SectionRequest $request)
    {
        Section::create([
            'name' => $request->input('name')
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
}
