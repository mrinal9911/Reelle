<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Models\UserLocation;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\RetryMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            // $request->validate(['id' => 'required']);
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
     * | Upload Photo
     */
    public function storePhoto(Request $request)
    {
        try {
            $request->validate([
                'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            $mUser  = new User();
            $userId = auth()->user()->id;
            // Store the image in public storage
            $path = $request->file('photo')->store('photos', 'public');
            $mUser->where('id', $userId)->update(['id_document_path' => ('storage/' . $path)]);

            return responseMsg(true, "Photo uploaded successfully.", "");
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
     * | submitResetPasswordForm
     * | Validate Password
     */
    public function submitForgetPasswordForm(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'email' => 'required|email|exists:users,email',
                'password' => 'required|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return validationError($validator);
            }

            $updatePassword = DB::table('password_reset_tokens')
                ->where([
                    'email' => $request->email,
                    'token' => $request->token
                ])->first();

            if (!$updatePassword)
                return back()->withInput()->with('error', 'Invalid token!');

            $user = User::where('email', $request->email)
                ->update(['password' => Hash::make($request->password)]);
            DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();

            return redirect('/api/password-changed')->with('message', 'Your password has been changed!');
            return responseMsg(true, "Your password has been changed succesfully", "");
        } catch (Exception $e) {
            return responseMsg(false, $e->getMessage(), "");
        }
    }

    /**
     * | Password Change Page
     */
    public function passwordChangedPage()
    {
        return view('passwordChanged');
    }

    /**
     * | Reset Password for logged in user
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

    /**
     * | Update User Location
     */
    public function updateLocation(Request $request)
    {
        try {
            $request->validate([
                'latitude'  => 'required|numeric',
                'longitude' => 'required|numeric',
                'heading'   => 'nullable|numeric',
            ]);

            $location = Auth::user()->location()->updateOrCreate(
                ['user_id' => Auth::id()],
                [
                    'latitude'  => $request->latitude,
                    'longitude' => $request->longitude,
                    'heading'   => $request->heading,
                ]
            );

            return responseMsg(true, 'Location updated', $location);
        } catch (Exception $e) {
            return responseMsg(false, $e->getMessage(), "");
        }
    }

    /**
     * | Get Near by User
     */
    public function getNearbyUsers(Request $request)
    {
        try {
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'radius' => 'required|numeric', // in kilometers
            ]);

            $lat = $request->latitude;
            $lng = $request->longitude;
            $radius = $request->radius;

            $users = UserLocation::selectRaw("
            user_id,
            latitude,
            longitude,
            heading,
            (
                6371 * acos(
                    cos(radians(?)) *
                    cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) *
                    sin(radians(latitude))
                )
            ) AS distance
        ", [$lat, $lng, $lat])
                ->having('distance', '<=', $radius)
                ->with('user')
                ->orderBy('distance')
                ->get();

            return responseMsg(true, 'Nearby users', $users);
        } catch (Exception $e) {
            return responseMsg(false, $e->getMessage(), "");
        }
    }
}
