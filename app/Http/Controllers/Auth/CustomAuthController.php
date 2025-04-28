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
            'account_type' => 'required|in:myself,others'
        ]);

        // You can save this account type in session or user profile later
        return response()->json([
            'message' => 'Account type selected successfully',
            'account_type' => $request->account_type
        ], 200);
    }

    // 2. Sign Up
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mother_tongue'   => 'required|string|max:100',
            'username'        => 'required|string|unique:users,username',
            'email'           => 'required|email|unique:users,email',
            'password'        => 'required|confirmed|min:6',
            'mobile_number'   => 'required|numeric|digits_between:8,15',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name'             => $request->username,
            'email'            => $request->email,
            'username'         => $request->username,
            'password'         => Hash::make($request->password),
            'mobile_number'    => $request->mobile_number,
            'language'         => $request->mother_tongue,
            'email_verified_at'=> null,
        ]);

        $otp = rand(100000, 999999);

        EmailOtp::create([
            'email'      => $user->email,
            'otp'        => $otp,
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        Mail::raw("Your OTP for Sheegravyvaham Matrimony is: $otp", function ($message) use ($user) {
            $message->to($user->email)->subject('Email OTP Verification');
        });

        return response()->json([
            'message' => 'User registered successfully. OTP sent to your email.',
            'user_id' => $user->id,
            'email'   => $user->email
        ], 200);
    }

    // 3. Verify OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required'
        ]);

        $record = EmailOtp::where('email', $request->email)
                          ->where('otp', $request->otp)
                          ->where('expires_at', '>=', Carbon::now())
                          ->first();

        if (!$record) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 422);
        }

        User::where('email', $request->email)->update([
            'email_verified_at' => now()
        ]);

        $user = User::where('email', $request->email)->first();
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
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('username', $request->username)->first();

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
