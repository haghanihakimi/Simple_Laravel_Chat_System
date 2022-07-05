<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Password;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ResetPasswordController extends Controller
{

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    public function authSendLink () {
        //Process to send password reset link if user has already logged in 
        //For logged in users, only those who have verified email address are eligible
        try {
            $user = User::find(auth()->user()->id);

            if ($user->hasVerifiedEmail()) {
                $status = Password::sendResetLink(
                    $user->only('email')
                );
                return $status === Password::RESET_LINK_SENT
                    ? response()->json(['code' => 200, 'message' => __($status)])
                    : response()->json(['code' => 500, 'message' => __($status)]);
            }
            return response()->json([
                'code' => 401,
                'message' => __('passwords.auth_unverified_email_pass_reset')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function guestSendLink (Request $request) {
        try {
            $request->validate(['email' => 'required|email|min:12|max:64']);

            $executed = RateLimiter::attempt(
                'send-message:'.$this->user->id,
                $perMinute = 1,
                function () {
                    $status = Password::sendResetLink(
                        $request->only('email')
                    );
                
                    return $status === Password::RESET_LINK_SENT
                        ? response()->json(['code' => 200, 'message' => __($status)])
                        : response()->json(['code' => 500, 'message' => __($status)]);
                }
            );
                
            if (! $executed) {
                return response()->json([
                    'code' => 429,
                    'message' => __('passwords.reset_throttled')
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }
}
