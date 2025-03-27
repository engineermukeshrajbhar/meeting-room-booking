<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginAttempt;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Add this import
use Illuminate\Validation\ValidationException; // Add this import
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function attemptLogin(Request $request)
    {
        Log::debug('Entering attemptLogin method');
        
        $email = $request->email;
        $ip = $request->ip();
        
        Log::debug("Login attempt for email: $email from IP: $ip");
        
        // Check if user is blocked
        $lastHour = Carbon::now()->subDay();
        $attempts = LoginAttempt::where('email', $email)
            ->where('ip_address', $ip)
            ->where('attempted_at', '>=', $lastHour)
            ->where('successful', false)
            ->count();

        Log::debug("Failed attempts in last 24 hours: $attempts");

        if ($attempts >= 3) {
            Log::debug('User is blocked due to too many attempts');
            return false;
        }

        $credentials = $this->credentials($request);
        $success = Auth::attempt($credentials, $request->filled('remember'));

        // Record login attempt
        LoginAttempt::create([
            'email' => $email,
            'ip_address' => $ip,
            'attempted_at' => now(),
            'successful' => $success
        ]);

        Log::debug('Login attempt recorded', ['success' => $success]);

        return $success;
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        Log::debug('Entering sendFailedLoginResponse method');
        
        $errors = [$this->username() => trans('auth.failed')];
        
        // Check if user is blocked
        $lastHour = Carbon::now()->subDay();
        $attempts = LoginAttempt::where('email', $request->email)
            ->where('ip_address', $request->ip())
            ->where('attempted_at', '>=', $lastHour)
            ->where('successful', false)
            ->count();

        Log::debug("Failed attempts count in sendFailedLoginResponse: $attempts");

        if ($attempts >= 3) {
            $errors = [$this->username() => 'Too many login attempts. Your account is blocked for 24 hours.'];
            Log::debug('User blocked message sent');
        }

        throw ValidationException::withMessages($errors);
    }
    protected function throttleKey(Request $request)
    {
        return Str::lower($request->input('email')) . '|' . $request->ip();
    }

    protected function hasTooManyLoginAttempts(Request $request)
    {
        return RateLimiter::tooManyAttempts($this->throttleKey($request), 3);
    }

    protected function incrementLoginAttempts(Request $request)
    {
        RateLimiter::hit($this->throttleKey($request), 1440 * 60); // 1440 minutes = 24 hours
    }

    protected function sendLockoutResponse(Request $request)
    {
        $seconds = RateLimiter::availableIn($this->throttleKey($request));
        return response()->json(['message' => "Too many login attempts. Please try again in $seconds seconds."], 429);
    }
}

