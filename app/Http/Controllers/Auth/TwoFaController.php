<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Auth\TwoFactorLoginRequestController as TFLR;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication as EnableTwoFa;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication as DisableTwoFa;
use Laravel\Fortify\Http\Controllers\TwoFactorQrCodeController as TwoFaQr;
use Laravel\Fortify\Http\Controllers\ConfirmedPasswordStatusController as PasswordStatus;
use Laravel\Fortify\Http\Controllers\ConfirmablePasswordController as ConfirmPassword;
use Laravel\Fortify\Events\RecoveryCodeReplaced;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Notification as Notifications;
use Carbon\Carbon;

class TwoFaController extends Controller
{
    protected function challengeView (Request $request) {
        if (!$request->session()->has('loginChallenge') || $request->session()->get('loginChallenge')['time'] <= now()->subMinute(10)) {
            $this->killChallengeSession($request);
            return redirect()->route('login');
        }
        
        return view('layouts.TwoFaChallenge');
    }

    /**
     * Checks & confirms if given 2-fa code is valid/correct or not
     *
     * @return void
     */
    protected function confirmTwoFaChallenge (Request $request, TFLR $tflr) {
        $this->twoFaTokenValidation($request);
        
        $user = User::find($request->session()->get('loginChallenge')['id']);

        foreach (json_decode(Crypt::decrypt($user->two_factor_recovery_codes, true)) as $recoverCode) {
            if ($request->input('code') === $recoverCode) {
                $user->replaceRecoveryCode($request->input('code'));
                event(new RecoveryCodeReplaced($user, $request->input('code')));

                $request->session()->put('replacedCode', "You recently used one of your backup codes.<br/>Please go to <a href='{{route(\"security.settings\")}}' target='_self'>security settings</a> section and check for newest generated backup codes.");

                return $this->validLogin($user, $request);
            }
        }
        
        if ($tflr->hasValidCode()) {
            return $this->validLogin($user, $request);
        }

        if ($request->session()->increment('twoFaFailed') >= 5) {
            $this->killChallengeSession($request);
        }

        return back()->with(['status' => 'An invalid code is used.<br/>Please check your authenticator app and use the newest generated code.<br/>If app is not accessible right now please use one of backup codes.']);
    }
    
    /**
     * Requires user password and check if password confirmation status is True OR False
     *
     * @return void
     */
    protected function confirmPassword (ConfirmPassword $confirmPassword, PasswordStatus $passwordStatus, Request $request) {
        try {
            $this->passwordValidation($request);

            $confirmPassword->store($request);
            
            return $passwordStatus->show($request);
        } catch(\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Checks if user confirmed their account password or not
     *
     * @return void
     */
    protected function passwordStatus (PasswordStatus $passwordStatus, Request $request) {
        try {
            return $passwordStatus->show($request);
        } catch(\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Enables Two Factor Authentication feature
     *
     * @return void
     */
    protected function enableTwoFa (EnableTwoFa $enableTwofa, Request $request) {
        try {
            $enableTwofa(auth()->user());

            $request->session()->flush();

            return $this->showQrCodeSvg($request);
        } catch(\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Disables Two Factor Authentication Feature
     *
     * @return void
     */
    protected function disableTwoFa (DisableTwoFa $disableTwoFa, Request $request) {
        try {
            $disableTwoFa(auth()->user());

            $request->session()->flush();

            return response()->json([
                'code' => 200,
                'status' => true
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * If Two Factor Authentication feature is ENABLED then returns QR Code of 2-FA in SVG format
     *
     * @return void
     */
    protected function showQrCodeSvg (Request $request) {
        try {
            $request->session()->flush();

            return auth()->user()->twoFactorQrCodeSvg();
        } catch(\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Displays all valid 2-factor authentication recovery codes
     *
     * @return array
     */
    protected function showRecoverCodes (Request $request) {
        try {
            $request->session()->flush();
            $recoveryCodes = [];

            foreach (json_decode(Crypt::decrypt(auth()->user()->two_factor_recovery_codes, true)) as $recoveries) {
                $recoveryCodes[] = trim($recoveries);
            }

            return response()->json([
                'code' => 200,
                'recoveries' => $recoveryCodes
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Validates provided passwords by users
     *
     * @return boolean
     */
    private function passwordValidation ($request) {
        return $this->validate($request, [
            "password" => "required|max:255|min:8",
        ]);
    }

    /**
     * Validates provided 2-FA code by users
     *
     * @return boolean
     */
    private function twoFaTokenValidation ($request) {
        return $this->validate($request, [
            "code" => ['required', 'max:25', 'string', 'min:6'],
        ]);
    }

    /**
     * Removes two created sessions keys for 2-Factor Authentication Challenge page
     *
     * @return void
     */
    private function killChallengeSession ($request) {
        $request->session()->remove('loginChallenge');
        $request->session()->remove('twoFaFailed');
    }

    private function validLogin($user, $request) {
        $this->killChallengeSession($request);

        Auth::login($user, 'on');

        $request->session()->regenerate();

        return redirect()->route('landing');
    }
}
