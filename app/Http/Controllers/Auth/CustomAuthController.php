<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\EmailOtp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\TempUser;


class CustomAuthController extends Controller
{
    public function selectLanguage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'language' => 'required|in:english,hindi,malayalam,tamil,other',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        session(['selected_language' => $request->language]);

        return response()->json([
            'status' => true,
            'message' => 'Language selected successfully.',
            'data' => [
                'selected_language' => $request->language,
            ],
        ], 200);
    }
        // 1. Account Selection
        public function accountSelection(Request $request)
        {
            $request->validate([
                'account_type' => 'required|string'
            ]);

            // Store selected account type in session or proceed to registration.
            session(['account_type' => $request->account_type]);

            return response()->json([
                'message' => 'Account type selected successfully',
                'account_type' => $request->account_type
            ], 200);
        }

        public function signup(Request $request)
        {
            // Validate the input
            $validator = Validator::make($request->all(), [
                'mother_tongue'   => 'required|string|max:100',
                'username'        => 'required|string|unique:temp_users,username|unique:users,username',
                'email'           => 'required|email|unique:temp_users,email|unique:users,email',
                'password'        => 'required|confirmed|min:6',
                'mobile_number'   => 'required|numeric|digits_between:8,15',
                'account_type'    => 'required|string'
            ]);

            // If validation fails, return errors
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // ✅ Delete any existing temporary user records with the same email or username
            TempUser::where('email', $request->email)->delete();
            TempUser::where('username', $request->username)->delete();

            // Create a new temporary user
            $tempUser = TempUser::create([
                'username'      => $request->username,
                'email'         => $request->email,
                'password'      => Hash::make($request->password),
                'mobile_number' => $request->mobile_number,
                'mother_tongue' => $request->mother_tongue,
                'account_type'  => $request->account_type
            ]);

            // Generate OTP
            $otp = rand(100000, 999999);

            // ✅ Clear any old OTP for the same email
            EmailOtp::where('email', $tempUser->email)->delete();

            // Create new OTP record
            EmailOtp::create([
                'email' => $tempUser->email,
                'otp' => $otp,
                'expires_at' => now()->addMinutes(10)
            ]);

            // Send OTP email
            Mail::raw("Your OTP for Sheegravyvaham Matrimony is: $otp", function ($message) use ($tempUser) {
                $message->to($tempUser->email)->subject('Email OTP Verification');
            });

            // Return response indicating the user was registered temporarily and OTP was sent
            return response()->json([
                'message' => 'User registered temporarily. OTP sent to your email.',
                'email' => $tempUser->email
            ]);
        }

    // 3. Verify OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required'
        ]);

        $emailOtp = EmailOtp::where('email', $request->email)
                    ->where('otp', $request->otp)
                    ->where('expires_at', '>=', now())
                    ->first();

        if (!$emailOtp) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 422);
        }

        $tempUser = TempUser::where('email', $request->email)->first();

        if (!$tempUser) {
            return response()->json(['message' => 'Temporary user not found.'], 404);
        }

        // Move data from temp_users to users
        $user = User::create([
            'name' => $tempUser->username,
            'username' => $tempUser->username,
            'email' => $tempUser->email,
            'password' => $tempUser->password,
            'mobile_number' => $tempUser->mobile_number,
            'language' => $tempUser->mother_tongue,
            'account_type' => $tempUser->account_type,
            'email_verified_at' => now()
        ]);

        // Clean up temp & OTP record
        $tempUser->delete();
        $emailOtp->delete();

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => 'OTP verified successfully.',
            'token' => $token,
            'user' => $user
        ]);
    }



    // 4. Login
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required', // can be username or email
            'password' => 'required'
        ]);

        $loginField = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($loginField, $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'token' => $token,
            'user' => $user
        ]);
    }


    // 5. Forgot Password
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $otp = rand(100000, 999999);
        EmailOtp::create([
            'email' => $request->email,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        Mail::raw("Your OTP for password reset is: $otp", function ($message) use ($request) {
            $message->to($request->email)->subject('Password Reset OTP');
        });

        return response()->json(['message' => 'OTP sent to email.'], 200);
    }

    // 6. Reset Password via OTP
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
            'new_password' => 'required|confirmed|min:6'
        ]);

        $record = EmailOtp::where('email', $request->email)
                          ->where('otp', $request->otp)
                          ->where('expires_at', '>=', Carbon::now())
                          ->first();

        if (!$record) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 422);
        }

        User::where('email', $request->email)->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json(['message' => 'Password reset successful.']);
    }

    // 7. Change Password (after login)
    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed|min:6'
        ]);

        $user = $request->user();

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['message' => 'Old password incorrect.'], 422);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json(['message' => 'Password changed successfully.']);
    }
}
