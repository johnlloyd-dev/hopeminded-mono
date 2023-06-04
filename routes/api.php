<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CheckAccessIdController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\QuizReportController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TextbookController;
use App\Http\Controllers\TextbookFlagController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/register', [RegisterController::class, 'register']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request){
        return Auth::user();
    });
    Route::post('/textbook/flag/add', [TextbookFlagController::class, 'storeFlags']);
    Route::get('/users/students/get', [UserController::class, 'getStudents']);
    Route::post('/users/students/add', [UserController::class, 'addStudent']);

    Route::get('/users/teachers/get', [TeacherController::class, 'getTeachers']);
    Route::post('/users/teachers/add', [TeacherController::class, 'addTeacher']);

    Route::get('flags/{flag}', [TextbookFlagController::class, 'getFlags']);

    Route::put('update-flag/{flag}', [TextbookFlagController::class, 'updateFlag']);

    Route::post('quiz/info/store/{flag}', [GameController::class, 'storeGameInfo']);
    Route::get('quiz/info/get', [GameController::class, 'getGameInfo']);
    Route::post('quiz/info/update/{id}', [GameController::class, 'updateGameInfo']);

    Route::get('quiz/reports/get', [GameController::class, 'getQuizReports']);

    Route::get('quiz-reports/get', [GameController::class, 'allGameRecords']);

    Route::get('/certificates/student/{studentId}/game-id/{gameFlag}', [CertificateController::class, 'getCertificates']);
    Route::post('/certificate/upload', [CertificateController::class, 'uploadFile']);
    Route::post('/certificate/delete', [CertificateController::class, 'deleteCertificate']);
    Route::get('/student-certificates/all', [CertificateController::class, 'getStudentCertificates']);

    Route::post('/textbook/add', [TextbookController::class, 'addTextbook']);

    Route::get('quiz-report/student/{studentId}', [QuizReportController::class, 'getStudentQuizReport']);

    Route::post('/logout', [LogoutController::class, 'logout']);
});

Route::get('/check-access-id', [CheckAccessIdController::class, 'checkAccessId']);