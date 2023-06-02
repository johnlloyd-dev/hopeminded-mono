<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Game;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;

class CertificateController extends Controller
{
    public function getCertificates($studentId, $gameId) {
        $gameFlag = Game::where('id', $gameId)->first()->flag;
        return Certificate::where('student_id', $studentId)
            ->where('game_flag', $gameFlag)
            ->get()
            ->map(function($certificate){
                $certificate->file_url = Storage::url($certificate->file);
                return $certificate;
            })->toArray();
    }

    public function getStudentCertificates() {
        $studentId = Student::where('user_id', Auth::user()->id)->first()->id;
        return Certificate::where('student_id', $studentId)
            ->get()
            ->map(function($certificate){
                $certificate->file_url = Storage::url($certificate->file);
                return $certificate;
            })->toArray();
    }

    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,pdf',
            'studentId' => 'required|integer',
            'gameId' => 'required|integer'
        ]);
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $path = $file->storeAs('certificates', $fileName, 'public');

            $textbookFlag = null;
            $gameFlag = Game::where('id', $request->gameId)->first()->flag;

            switch ($request->gameId) {
                case 1:
                    $textbookFlag = 'alphabet-words';
                    break;
                case 2:
                    $textbookFlag = 'vowel-consonants';
                    break;
                case 3:
                    $textbookFlag = 'alphabet-letters';
                    break;
            }

            Certificate::create([
                'student_id' => $request->studentId,
                'textbook_flag' => $textbookFlag, 
                'game_flag' => $gameFlag, 
                'file' => $path
            ]);


            return response()->json(['message' => 'A certificate is added successfully.']);
        }

        return response()->json(['error' => 'File not found.'], 400);
    }

    public function deleteCertificate(Request $request) {
        $isDeleted = Storage::disk('public')->delete($request->file);
        if($isDeleted) {
            Certificate::find($request->id)->delete();
            return response()->json(['message' => 'A certificate is deleted successfully.']);
        } else {
            return response()->json(['message' => 'Something went wrong.'], 500);
        }
    }
}
