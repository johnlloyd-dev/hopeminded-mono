<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Game;
use App\Models\Student;
use App\Models\Teacher;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function getCertificates($studentId, $gameId)
    {
        $gameFlag = Game::where('id', $gameId)->first()->flag;
        return Certificate::where('student_id', $studentId)
            ->where('game_flag', $gameFlag)
            ->get()
            ->map(function ($certificate) {
                $certificate->file_url = json_decode($certificate->file);
                return $certificate;
            })->toArray();
    }

    public function getStudentCertificates()
    {
        $studentId = Student::where('user_id', Auth::user()->id)->first()->id;
        return Certificate::where('student_id', $studentId)
            ->get()
            ->map(function ($certificate) {
                $certificate->file_url = json_decode($certificate->file);
                return $certificate;
            })->toArray();
    }

    public function uploadFile(Request $request)
    {
        $flag = $request->input('flag');
        $studentId = $request->input('studentId');
        $gameId = $request->input('gameId');

        $textbookFlag = $flag;
        $gameFlag = Game::where('id', $gameId)->first()->flag;

        $student = Student::find($studentId);
        $studentName = $student->first_name . ' ' . ($student->middle_name ?? NULL) . ($student->middle_name ? ' ' : '') . $student->last_name;

        $filterFlag = null;
        switch ($flag) {
            case 'alphabet-words':
                $filterFlag = 'ALPHABETS/WORDS';
                break;

            case 'vowel-consonants':
                $filterFlag = 'VOWELS/CONSONANTS';
                break;

            default:
                $filterFlag = 'ALPHABETS/LETTERS';
                break;
        }

        $data = [
            'student_name' => $studentName,
            'filtered_flag' => $filterFlag
        ];

        $pdf = Pdf::loadView('layouts.certificate', compact('data'))->setPaper('a4', 'landscape');

        $expiresAt = new DateTime();
        $expiresAt->modify('+1 year');

        // $certificate = $pdf->output();
        $firebase_storage_path = 'certificates/';

        $name = 'Certificate' . rand(100000, 999999) . '-' . $textbookFlag . '.pdf';
        $localfolder = public_path('firebase-temp-uploads') . '/' . $name;
        // $extension = $certificate->getClientOriginalExtension();
        // $file      = $name . '.' . $extension;

        $pdf->save($localfolder);

        // $certificate->move($localfolder, $file);
        $uploadedfile = fopen($localfolder, 'r');
        $path = app('firebase.storage')->getBucket()->upload($uploadedfile, [
            'name' => $firebase_storage_path . $name
        ])->signedUrl($expiresAt);
        unlink($localfolder);

        Certificate::create([
            'student_id' => $request->studentId,
            'textbook_flag' => $textbookFlag,
            'game_flag' => $gameFlag,
            'file_name' => $name,
            'file' => json_encode($path)
        ]);


        return response()->json(['message' => 'A certificate is added successfully.']);
    }

    public function deleteCertificate(Request $request)
    {
        // $isDeleted = Storage::disk('public')->delete($request->file);
        Certificate::find($request->id)->delete();
        return response()->json(['message' => 'A certificate is deleted successfully.']);
    }
}
