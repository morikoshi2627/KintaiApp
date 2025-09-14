<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserAuthRegisterController;
use App\Http\Controllers\UserAuthLoginController;
use App\Http\Controllers\UserAttendanceController;
use App\Http\Controllers\UserAttendanceRequestController;
use App\Http\Controllers\AdminAuthLoginController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\AdminStaffController;
use App\Http\Controllers\AdminStaffAttendanceController;
use App\Http\Controllers\AdminAttendanceRequestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
|--------------------------------------------------------------------------
| 一般ユーザー画面
|--------------------------------------------------------------------------
*/

// 会員登録
Route::get('/register', [UserAuthRegisterController::class, 'create'])->name('user.register');
Route::post('/register', [UserAuthRegisterController::class, 'store']);

// ログイン
Route::get('/login', [UserAuthLoginController::class, 'create'])->name('login');
Route::post('/login', [UserAuthLoginController::class, 'store']);

// 一般ユーザー logout
Route::post('/logout', [UserAuthLoginController::class, 'destroy'])->name('logout');

// 認証必須ルート（一般ユーザー）
Route::middleware('auth')->group(function () {

// 出勤登録
Route::get('/attendance', [UserAttendanceController::class, 'index'])->name('attendance.index');
Route::post('/attendance', [UserAttendanceController::class, 'store'])->name('attendance.store');

// 勤怠一覧
Route::get('/attendance/list', [UserAttendanceController::class, 'list'])->name('attendance.list');

// 勤怠詳細
Route::get('/attendance/detail/{id}', [UserAttendanceController::class, 'show'])->name('attendance.detail');
Route::put('/attendance/detail/{id}', [UserAttendanceController::class, 'update'])->name('attendance.update');

// 修正申請一覧（ユーザー用）
Route::get('/stamp_correction_request/list', [UserAttendanceRequestController::class, 'index'])
  ->name('attendance_request.list');
});
// 修正申請作成（ユーザー用）
Route::post('/stamp_correction_request/store', [UserAttendanceRequestController::class, 'store'])
  ->name('attendance_request.store');

// 修正申請詳細（ユーザー用）
Route::get('/stamp_correction_request/{id}', [UserAttendanceRequestController::class, 'show'])
  ->name('attendance_request.show');

Route::get('/attendance/create', [UserAttendanceController::class, 'create'])->name('attendance.create');

/*
|--------------------------------------------------------------------------
| 管理者画面
|--------------------------------------------------------------------------
*/

// 管理者ログイン
Route::get('/admin/login', [AdminAuthLoginController::class, 'create'])->name('admin.login');
Route::post('/admin/login', [AdminAuthLoginController::class, 'store'])->name('admin.login.store');

// 管理者 logout
Route::post('/admin/logout', [AdminAuthLoginController::class, 'destroy'])->name('admin.logout');

// 認証必須ルート（管理者）
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(
  function () {

// 勤怠一覧
Route::get('/attendances/list', [AdminAttendanceController::class, 'index'])->name('attendances');

// 勤怠新規作成
Route::get('/attendances/create/{user}/{date}', [AdminAttendanceController::class, 'create'])->name('attendances.create');
Route::post('/attendances', [AdminAttendanceController::class, 'store'])->name('attendances.store');

Route::get('/attendances/{id}', [AdminAttendanceController::class, 'show'])->name('attendances.show');

Route::put('/attendances/{id}', [AdminAttendanceController::class, 'update'])->name('attendances.update');

// スタッフ一覧
Route::get('/staff/list', [AdminStaffController::class, 'index'])->name('users');

// スタッフ別勤怠一覧
Route::get('/attendance/staff/{id}', [AdminStaffAttendanceController::class, 'index'])->name('user.attendances');
Route::get('/attendance/staff/{id}/export', [AdminStaffAttendanceController::class, 'export'])->name('user.attendances.export');

// 修正申請一覧
Route::get('/stamp_correction_request/list', [AdminAttendanceRequestController::class, 'index'])->name('requests');
Route::get('/stamp_correction_request/approve/{attendance_correct_request}', [AdminAttendanceRequestController::class, 'show'])->name('request.detail');
Route::put('/stamp_correction_request/approve/{attendance_correct_request}', [AdminAttendanceRequestController::class, 'update'])->name('request.update');
}
);