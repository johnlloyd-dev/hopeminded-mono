<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade\Pdf;

class PDFController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $flag = $request->query('flag');
        $studentId = $request->query('studentId');

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

        return $pdf->stream('certificate.pdf');
    }
}
