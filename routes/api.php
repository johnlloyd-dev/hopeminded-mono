<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CheckAccessIdController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PassingPercentageController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\PerfectScoreController;
use App\Http\Controllers\QuantityRequirementController;
use App\Http\Controllers\QuizReportController;
use App\Http\Controllers\SkillTestController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExcelCSVController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\TextbookAlphabetController;
use App\Http\Controllers\TextbookNumberController;

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
    Route::get('/user', function (Request $request) {
        return Auth::user();
    });
    Route::get('/users/students/get', [UserController::class, 'getStudents']);
    Route::post('/users/students/add', [UserController::class, 'addStudent']);

    Route::get('/users/teachers/get', [TeacherController::class, 'getTeachers']);
    Route::post('/users/teachers/add', [TeacherController::class, 'addTeacher']);

    Route::post('quiz/info/store/{flag}', [GameController::class, 'storeGameInfo']);
    Route::get('quiz/info/get', [GameController::class, 'getGameInfo']);
    Route::post('quiz/info/update/{id}', [GameController::class, 'updateGameInfo']);

    Route::get('quiz/reports/get', [GameController::class, 'getQuizReports']);

    Route::get('skill-test/reports/get', [SkillTestController::class, 'getAllSkillTests']);

    Route::get('quiz-reports/get', [GameController::class, 'allGameRecords']);

    Route::get('/certificates/student/{studentId}/game-id/{gameFlag}', [CertificateController::class, 'getCertificates']);
    Route::post('/certificate/upload', [CertificateController::class, 'uploadFile']);
    Route::post('/certificate/delete', [CertificateController::class, 'deleteCertificate']);
    Route::get('/student-certificates/all', [CertificateController::class, 'getStudentCertificates']);

    Route::post('/textbook/add', [TextbookAlphabetController::class, 'addTextbook']);
    Route::post('/textbook/alphabets-words/add', [TextbookAlphabetController::class, 'addTextbookAlphabetWords']);
    Route::delete('textbook/delete/{textbookId}', [TextbookAlphabetController::class, 'deleteTextbook']);

    Route::get('quiz-report/student/{studentId}', [QuizReportController::class, 'getStudentQuizReport']);
    Route::get('/skill-test/fetch/{flag}', [SkillTestController::class, 'getSkillTest']);

    Route::get('alphabets-letters/get', [TextbookAlphabetController::class, 'getAlphabetsLetters']);
    Route::get('vowels-consonants/get', [TextbookAlphabetController::class, 'getVowelsConsonants']);
    Route::get('alphabets-words/get', [TextbookAlphabetController::class, 'getAlphabetsWords']);

    Route::prefix('/textbook')->group(function () {
        Route::get('/numbers', [TextbookNumberController::class, 'index']);
        Route::post('/number', [TextbookNumberController::class, 'addTextbookItem']);
        Route::delete('/number/{id}', [TextbookNumberController::class, 'deleteTextbookItem']);
    });

    Route::get('students-of-teacher/{teacherId}', [TeacherController::class, 'getStudents']);

    Route::get('profile/get', [StudentController::class, 'getProfile']);
    Route::post('profile/update', [StudentController::class, 'updateProfile']);

    Route::get('perfect-score/get', [PerfectScoreController::class, 'getPerfectScore']);
    Route::post('perfect-score/modify', [PerfectScoreController::class, 'modifyPerfectScore']);

    Route::post('skill-test-score/modify', [SkillTestController::class, 'updateScore']);
    Route::post('skill-test-mark/modify', [SkillTestController::class, 'updateMark']);

    Route::get('notifications/get', [NotificationController::class, 'getNotifications']);
    Route::put('retake/skill-test/allow/{retakeId}', [SkillTestController::class, 'allowRetake']);
    Route::put('retake/quiz/allow/{retakeId}', [GameController::class, 'allowRetake']);

    Route::post('/logout', [LogoutController::class, 'logout']);

    Route::post('seed', [TextbookAlphabetController::class, 'seedTextbook']);

    Route::post('/skill-test/upload', [SkillTestController::class, 'addSkillTest']);
    Route::put('/skill-test/update/{skillTestId}', [SkillTestController::class, 'updateSkillTest']);
    Route::post('/quiz-mistake/store',  [GameController::class, 'addQuizMistakeRecord']);

    Route::get('/get-mistakes/{studentId}',  [GameController::class, 'getWeaknessesData']);

    Route::put('/notification/update/{notificationId}',  [NotificationController::class, 'updateNotification']);

    Route::get('/available-ratake/quiz/get',  [QuizReportController::class, 'getAvailableQuizRetakes']);

    Route::get('/passing-percentage/get',  [PassingPercentageController::class, 'index']);
    Route::get('/passing-percentage/all/get',  [PassingPercentageController::class, 'getAllPassingPercentage']);
    Route::put('/passing-percentage/update/{id}',  [PassingPercentageController::class, 'updatePassingPercentage']);

    Route::get('/quiz-statistics',  [QuizReportController::class, 'getStatistics']);
    Route::get('/quiz-statistics/summary',  [QuizReportController::class, 'getStatisticsSummary']);

    Route::get('/quantity-requirements/get',  [QuantityRequirementController::class, 'index']);
    Route::put('/quantity-requirement/update/{id}',  [QuantityRequirementController::class, 'updateQuantityRequirement']);

    Route::put('/status/update/user/{userId}',  [UserController::class, 'updateStatus']);

    Route::get('excel-csv-file', [ExcelCSVController::class, 'index']);
    Route::post('import-excel-csv-file', [ExcelCSVController::class, 'importExcelCSV']);
    Route::get('export-excel-csv-file/{slug}', [ExcelCSVController::class, 'exportExcelCSV']);

    Route::prefix('/sections')->group(function () {
        Route::get('get', [SectionController::class, 'index']);
        Route::post('add', [SectionController::class, 'addSection']);
        Route::put('update/{id}', [SectionController::class, 'UpdateSection']);
        Route::delete('delete/{id}', [SectionController::class, 'deleteSection']);
    });

    Route::put('/students/section/update/{student}',  [StudentController::class, 'updateStudentSection']);

    Route::put('/students/rank/top-ten',  [StudentController::class, 'getTop10Students']);
    Route::get('/section/students',  [SectionController::class, 'getStudentsOfSection']);
    Route::get('/section/students/not-in-section',  [SectionController::class, 'getStudentsNotInSection']);
    Route::post('/section/students',  [SectionController::class, 'addStudentsToSection']);
    Route::delete('/section/student/{studentId}',  [SectionController::class, 'removeStudentFromSection']);
});
Route::post('reset-password/send-link', [EmailController::class, 'sendResetPasswordLink']);
Route::get('/check-access-id', [CheckAccessIdController::class, 'checkAccessId']);
Route::get('/reset-password/form', function () {
    return redirect('/reset-password');
});

Route::get('/certificate/pdf/generate',  [PDFController::class, 'index']);
Route::post('/reset-password', [EmailController::class, 'resetPassword']);
