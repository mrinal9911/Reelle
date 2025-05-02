<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserSettingsRequest;
use App\Models\UserSetting;
use Illuminate\Http\Request;

class UserSettingController extends Controller
{
    public function update(UpdateUserSettingsRequest $request)
    {
        $user = auth()->user();

        $settings = $user->settings ?? new UserSetting(['user_id' => $user->id]);
        $settings->fill($request->validated());
        $settings->save();

        return response()->json(['message' => 'Settings updated successfully.']);
    }
}
