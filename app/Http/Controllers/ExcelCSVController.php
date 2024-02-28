<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\StudentsExport;
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;

class ExcelCSVController extends Controller
{
    public function index()
    {
        return view('excel-csv-import');
    }

    public function importExcelCSV(Request $request)
    {
        $request->validate([
            'csv_file' => 'required',
        ]);

        Excel::import(new StudentsImport, $request->file('csv_file'));

        return response()->json(['message' => 'Students have been imported successfully.']);
    }

    public function exportExcelCSV($slug)
    {
        return Excel::download(new StudentsExport, 'users.' . $slug);
    }
}
