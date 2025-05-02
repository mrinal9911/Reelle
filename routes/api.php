<?php

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
