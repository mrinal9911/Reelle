<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    /**
     * | User Registration
     */
    public function userRegistration(StoreUserRequest $request)
    {
        try {
            $request->validate(['id' => 'required']);
            $mreqs = $this->makeUserRequest($request);
            $mreqs = array_merge($mreqs, ['password' => Hash::make($request->password)]);
            $mUser = new User();
            $mUser->addUser($mreqs);
            return responseMsg(true, "User Registered Successfully !! Please Continue to Login", "");
        } catch (Exception $e) {
            return responseMsg(false, $e->getMessage(), "");
        }
    }

    /**
     * | User Login
     */
    public function login(Request $request)
    {
        $validated = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required',
                'type' => "nullable|in:mobile"
            ]
        );
        if ($validated->fails())
            return validationError($validated);
        try {
            $mUser = new User();
            $user  = $mUser->getUserByEmail($request->email);
            if (!$user)
                throw new Exception("Please enter a valid email.");
            if ($user->suspended == true)
                throw new Exception("You are not authorized to log in!");
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('my-app-token')->plainTextToken;

                $data['token'] = $token;
                $data['userDetails'] = $user;
                return responseMsg(true, "You have Logged In Successfully", $data);
            }

            throw new Exception("Invalid Credentials");
        } catch (Exception $e) {
            return responseMsg(false, $e->getMessage(), "");
        }
    }

    /**
     * | User Registration
     */
    public function userDetails()
    {
        try {
            $userId = auth()->user()->id;
            $mUser = new User();
            $userDtls = $mUser->getUserDetails($userId);
            return responseMsg(true, "User Details", $userDtls);
        } catch (Exception $e) {
            return responseMsg(false, $e->getMessage(), "");
        }
    }

    /**
     * | Update User
     */
    public function updateUserProfile(UpdateUserRequest $request)
    {
        try {
            $userId = auth()->user()->id;
            $mreqs  = $this->makeUserRequest($request);
            $mreqs  = array_merge($mreqs, ['id' => $userId]);
            $mUser  = new User();
            $mUser->editUser($mreqs);
            return responseMsg(true, "User Updated Successfully", "");
        } catch (Exception $e) {
            return responseMsg(false, $e->getMessage(), "");
        }
    }

    /**
     * | Forgot Password
     */
    public function forgotPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                // 'email' => 'required|email|exists:users',
            ]);
            if ($validator->fails())
                return validationError($validator);

            $token = Str::random(64);
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]);

            Mail::send('email.reset_password', ['token' => $token], function ($message) use ($request) {
                $message->to($request->email);
                $message->subject('Reset Password');
            });

            return responseMsg(true, "We have sent email for password reset link!", "");
        } catch (Exception $e) {
            return responseMsg(false, $e->getMessage(), "");
        }
    }

    /**
     * | Reset Password Form
     */
    public function showResetPasswordForm($token)
    {
        return view('auth.forgetPasswordLink', ['token' => $token]);
    }

    /**
     * | Validate Password
     */
    public function validatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return validationError($validator);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => __($status)], 200);
        }

        return response()->json(['message' => __($status)], 400);
    }

    /**
     * | Reset Password
     */
    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email'       => 'required',
                'oldPassword' => 'required',
                'newPassword' => 'required',
            ]);
            if ($validator->fails())
                return validationError($validator);

            $refUser = User::where('email', $request->email)
                ->first();

            // If the User is existing
            if ($refUser) {
                // Checking Password
                if (Hash::check($request->password, $refUser->password)) {
                    $refUser->password = Hash::make($request->newPassword);
                    $refUser->save();

                    return responseMsg(true, "Password changed successfully", "");
                }

                // If Password Does not Matched
                else
                    return responseMsg(false, "Password not matched", "");
            }
            // If the UserName is not Existing
            else
                return responseMsg(false, "User not found", "");
        } catch (Exception $e) {
            return responseMsg(false, $e->getMessage(), "");
        }
    }

    /**
     * | Logout
     */
    public function logout(Request $request)
    {
        try {
            $token = $request->user()->currentAccessToken();
            $token->expires_at = Carbon::now();
            $token->save();
            return responseMsg(true, "You have Logged Out", "");
        } catch (Exception $e) {
            return responseMsg(false, $e->getMessage(), "");
        }
    }

    /**
     * | Make User Request Format
     */
    public function makeUserRequest($request)
    {
        return [
            "nickname"            => $request->nickname,
            "first_name"          => $request->firstName,
            "last_name"           => $request->lastName,
            "email"               => $request->email,
            "phone"               => $request->phone,
            "dob"                 => $request->dob,
            "gender"              => $request->gender,
            "occupation"          => $request->occupation,
            "relationship_status" => $request->relationshipStatus,
            "primary_language"    => $request->primaryLanguage,
            "secondary_language"  => $request->secondaryLanguage,
            "education_level"     => $request->educationLevel,
            "net_worth_range"     => $request->netWorthRange,
            "id_document_path"    => $request->idDocumentPath,

            // "govt_verified"       => $request->govtVerified ?? 0,
            // "two_fa_enabled"      => $request->twoFaEnabled ?? 0,
            // "device_logs"         => $request->deviceLogs,
            // "email_verified_at"   => $request->emailVerifiedAt,
            // "remember_token"      => $request->rememberToken,
        ];
    }
}
