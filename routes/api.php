<?php

use App\Http\Controllers\AssetsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UserSettingController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * | Created On: 01-05-2025
 * | Created By: Mrinal Kumar
 * | CRUD of Members
 */
Route::controller(UserProfileController::class)->group(function () {
    Route::get('user/profile', 'createMember');        #_fetch user profile
    Route::put('user/profile', 'memberList');         #_update editable profile fields
    Route::post('user/verify-id', 'deleteMember');     #_upload ID/passport
    Route::post('user/privacy-settings', 'updateMember');     #_update toggles (age, net worth, visibility, etc.)
    Route::post('user/consents', 'updateMember');     #_event & brand permission settings
    Route::post('user/security/2fa', 'updateMember');     #_enable/disable 2FA
    Route::post('user/devices', 'updateMember');     #_list logged devices

});

Route::middleware('auth:sanctum')->group(function () {
    // User Profile
    Route::get('user/profile', [UserProfileController::class, 'show']);
    Route::put('user/profile', [UserProfileController::class, 'update']);

    // User Settings
    Route::get('user/settings', [UserSettingController::class, 'show']);
    Route::put('user/settings', [UserSettingController::class, 'update']);
});

/**
 * | Created On: 02-05-2025
 * | Created By: Mrinal Kumar
 * | User Register & Login
 */
Route::controller(UserController::class)->group(function () {
    Route::post('auth/register', 'userRegistration');
    Route::post('auth/login', 'login');
    Route::get('user/profile', 'userDetails')->middleware('auth:sanctum');
    Route::post('user/profile/update', 'updateUserProfile')->middleware('auth:sanctum');
    Route::post('auth/forgot-password', 'forgotPassword'); //sendResetLinkEmail
    Route::post('auth/validate-password', 'validatePassword');
    Route::post('auth/reset-password', 'resetPassword')->middleware('auth:sanctum');
    Route::post('auth/logout', 'logout')->middleware('auth:sanctum');

    Route::get('auth/reset-password/{token}',  'showResetPasswordForm')->name('reset.password.get');
    Route::post('auth/reset-password',  'submitResetPasswordForm')->name('reset.password.post');
});

Route::prefix('assets')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [AssetsController::class, 'index']);
    Route::post('/', [AssetsController::class, 'store']);
    Route::get('{asset}', [AssetsController::class, 'show']);
    Route::put('{asset}', [AssetsController::class, 'update']);
    Route::delete('{asset}', [AssetsController::class, 'destroy']);

    // Extra actions
    Route::post('{asset}/report-lost', [AssetsController::class, 'reportLost']);
    Route::post('{asset}/toggle-sale', [AssetsController::class, 'toggleSale']);
    Route::post('{asset}/toggle-visibility', [AssetsController::class, 'toggleVisibility']);
});
