<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Auth\Http\Requests\LoginRequest;
use Modules\Auth\Http\Requests\RegisterRequest;
use Modules\Auth\Models\User;
use Modules\Auth\Services\AuthService;
use Modules\Auth\Services\OtpService;

class AuthController extends Controller
{
    private AuthService $authService;
    private OtpService $otpService;

    public function __construct(AuthService $authService,OtpService $otpService)
    {
        $this->authService = $authService;
        $this->otpService = $otpService;
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $user = $this->authService->register($data);
        if($user)
        {
            $this->sendWhatsAppOtp($user['user']);
            return $this->respondCreated($user,'User registered successfully');
        }
        else
        {
            return $this->respondNotFound(null,'User registration failed');
        }
    }
    public function login(LoginRequest $request)
    {
        $data= $request->validated();
        $user = $this->authService->login($data);
        if($user)
        {
            // activity()->causedBy($user['user'])->useLog('auth')->log('User logged in');
            return $this->respondOk($user,'User logged in successfully');
        }
        else
        {
            return $this->respondNotFound(null,'Invalid credentials');
        }
    }

    public function adminLogin(LoginRequest $request)
    {
        $data = $request->validated();
        $user = $this->authService->adminLogin($data);
        if ($user) {
            return $this->respondOk($user, 'Admin logged in successfully');
        } else {
            return $this->respondNotFound(null, 'Invalid credentials');
        }
    }
    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->tokens()->delete();
            return $this->respondOk(null, 'User logged out successfully');
        } else {
            return $this->respondNotFound(null, 'User not found');
        }
    }

    public function forgetPassword(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|numeric|exists:users,phone',
        ]);

        $user = User::where('phone', $validated['phone'])->first();

        if ($user) {
            $this->sendWhatsAppOtp($user);
            return $this->respondOk(null, 'Please check your phone');
        } else {
            return $this->respondNotFound(null, 'Something went wrong please try again later');
        }
    }

    public function checkPhoneOTPForgetPassword(Request $request)
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'exists:users,phone'],
            'phoneOtp' => 'required|numeric',
        ]);

        $user = User::where('phone', $validated['phone'])->first();

        $maxAttempts = 5;
        $lockDuration = 5;

        if ($user->otp_sent_at < now()->subMinutes($lockDuration)) {
            $user->update(['otp_attempts' => 0]);
        }

        if ($user->otp_attempts >= $maxAttempts) {
            return $this->respondNotFound(null, 'Maximum OTP attempts exceeded. Please try again after 5 minutes.');
        }

        if ($user->otp_expires_at < now()) {
            return $this->respondNotFound(null, 'OTP has expired. Please request a new one.');
        }

        if ($validated['phoneOtp'] != $user->otp) {
            $user->increment('otp_attempts');
            $user->update(['otp_sent_at' => now()]);
            return $this->respondNotFound(null, 'Invalid OTP');
        }

        $user->update([
                'otp' => null,
                'otp_expires_at' => null,
                'otp_attempts' => 0,
                'otp_verified_at' => now(),
            ]);
            $user->save();

            $user->tokens()->delete();

            $token=  $user->createToken('auth_token')->plainTextToken;

            return $this->respondOk($token, 'OTP verified successfully');

    }

    public function resetPassword(Request $request)
    {
        $fields = $request->validated();

        $user = auth('sanctum')->user();

        $this->authService->resetPassword($user, $fields['password']);

        return $this->respondOk(null, 'Password reset successfully');
    }

    public function sendWhatsAppOtp($user)
    {
        $otp = rand(10000, 99999);
        $formattedPhone=$user->country_code . $user->phone;

        if($this->otpService->sendOTPViaWhatsApp(trim($formattedPhone, '+'),$otp)) {
            $user->update([
                'otp' => $otp,
                'otp_sent_at' => now(),
                'otp_expires_at' => now()->addMinutes(10),
                'otp_attempts' => $user->otp_attempts + 1,
            ]);
            $user->save();
            return true;
        }
        else
        {
            return false;
        }
    }

    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|numeric',
            'otp' => 'required|numeric|digits:5',
        ]);

        $user = User::where('phone', $validated['phone'])->first();

        if (!$user) {
            return $this->respondNotFound(null, 'Phone not found');
        }

        $maxAttempts = 5;
        $lockDuration = 5;

        if ($user->otp_sent_at < now()->subMinutes($lockDuration)) {
            $user->update(['otp_attempts' => 0]);
        }

        if ($user->otp_attempts >= $maxAttempts) {
            return $this->respondNotFound(null, 'Maximum OTP attempts exceeded. Please try again after 5 minutes.');
        }

        if ($user->otp_expires_at < now()) {
            return $this->respondNotFound(null, 'OTP has expired. Please request a new one.');
        }

        if ($validated['otp'] != $user->otp) {
            $user->increment('otp_attempts');
            $user->update(['otp_sent_at' => now()]);
            return $this->respondNotFound(null, 'Invalid OTP');
        }

        $user->update(['otp_verified_at' => Carbon::now(), 'otp' => null, 'otp_attempts' => 0]);

        return $this->respondOk($user, 'phone verified successfully.');
    }
    public function  resendOtp(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|numeric',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return $this->respondNotFound(null, 'Phone not found');
        }

        if (!$user) {
            return $this->respondNotFound(null, 'Phone not found');
        }

        $maxAttempts = 5;
        $lockDuration = 5;

        if ($user->otp_sent_at < now()->subMinutes($lockDuration)) {
            $user->update(['otp_attempts' => 0]);
        }

        if ($user->otp_attempts >= $maxAttempts) {
            return $this->respondNotFound(null, 'Maximum OTP attempts exceeded. Please try again after 5 minutes.');
        }

        if ($this->sendWhatsAppOtp($user)) {
            return $this->respondOk(null, 'Please check your phone.');
        }

        return $this->respondNotFound(null, 'Something went wrong please try again later');
    }

}
