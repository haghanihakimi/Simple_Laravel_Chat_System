<?php

namespace App\Http\Controllers\Environments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\DeletedAccount;
use App\Models\Message;
use App\Models\Participant;
use App\Models\Conversation;
use App\Models\Notification as Notifications;
use App\Jobs\EmailChangeJob;
use App\Jobs\ClearUserHistoryJob;
use Illuminate\Support\Facades\Session;

class SettingsController extends Controller
{
    public function view (Request $request) {
        if (Auth::check()) {
            $user = User::find(auth()->user()->id);
            $settings = json_decode(Redis::get('profiles:username:'.$user->uid));

            return view('layouts.SecuritySettings', compact(
                'user',
                'settings'
            ));
        }

        return redirect()->route('/');
    }

    protected function getUser () {
        if (Auth::check()) {
            $user = User::find(auth()->user()->id);
            
            return response()->json([
                'code' => 200,
                'user' => [
                    'email' => $user->email,
                    'uid' => $user->public_uid,
                    'is_twofa' => empty($user->two_factor_secret) ? false : true,
                ]
            ]);
        }
    }

    protected function saveChanges (Request $request) {
        if (Auth::check()) {
            try {
                $this->validate($request, [
                    'email' => ['required', 'email', 'confirmed', 'max:64', 'min:12', 'unique:users'],
                    'password' => ['required', 'string', 'min:8', 'max:64']
                ]);

                $user = User::find(auth()->user()->id);
                
                if (Hash::check($request->password, $user->password)) {
                    $user->forceFill([
                        'email' => $request->email_confirmation,
                        'email_verified_at' => null
                    ])->save();
                    
                    EmailChangeJob::dispatch($user)->delay(now()->addSeconds(10));
                
                    return response()->json([
                        'code' => 200,
                        'message' => __('verification.successful.text'),
                        'resendLink' => __('verification.successful.link')
                    ]);
                }
                
                return response()->json([
                    'code' => 401,
                    'message' => '<p class="error">Unauthorized access!</p>'
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'code' => 500,
                    'message' => '<p class="error">'.$e->getMessage().'</p>'
                ]);
            }
        }
    }

    protected function accountDelete (Request $request) {
        try {
            if (Auth::check()) {
                $user = User::find(auth()->user()->id);
                
                $user->update([
                    'is_restorable' => true,
                    'is_active' => false
                ]);
                
                if ($user->save()){ 
                    $user->delete();
                    
                    $this->signoutUser($request);
                    
                    return response()->json([
                        'code' => 200,
                        'message' => ''
                    ]);
                }

                return response()->json([
                    'code' => 500,
                    'message' => "Unable to process this request at this moment.<br/>Please try again later."
                ]);
            }
            return response()-json([
                'code' => 401,
                'message' => "Unauthorized Request."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }

    protected function collectPreferences ()
    {
        if (Auth::check()) {
            try {
                $settings = json_decode(Redis::get('profiles:username:'.auth()->user()->uid));
    
                return response()->json(['code' => 200, 'title' => 'Mode changed successfully!', 'message' => 'Mode changed successfully', 'settings' => $settings]);
            } catch (\Exception $e) {
                return response()->json([
                    'code' => 500, 
                    'title' => 'Mode changing error!', 
                    'message' => $e->getMessage()
                ]);
            }
        }
    }

    protected function updateDarkMode (Request $request)
    {
        if (Auth::check()) {
            try {
                $settings = json_decode(Redis::get('profiles:username:'.auth()->user()->uid));
                Redis::set('profiles:username:'.auth()->user()->uid, json_encode([
                    "user_id" => auth()->user()->id,
                    "dark_mode" => $settings->dark_mode ? 0 : 1,
                    "notification_sound" => $settings->notification_sound,
                    "message_sound" => $settings->message_sound,
                    "created_at" => $settings->created_at,
                    "upated_at" => now()
                ]));
                $settings = json_decode(Redis::get('profiles:username:'.auth()->user()->uid));
    
    
                return response()->json(['code' => 200, 'title' => 'Mode changed successfully!', 'message' => 'Mode changed successfully', 'settings' => $settings]);
            } catch(\Exception $e) {
                session()->regenerate();
                return response()->json(['code' => 500, 'title' => 'Mode changing error!', 'message' => $e->getMessage()]);
            }
        }
    }

    protected function updateNotificationSound (Request $request)
    {
        if (Auth::check()) {
            try {
                $settings = json_decode(Redis::get('profiles:username:'.auth()->user()->uid));
                Redis::set('profiles:username:'.auth()->user()->uid, json_encode([
                    "user_id" => auth()->user()->id,
                    "dark_mode" => $settings->dark_mode,
                    "notification_sound" => $settings->notification_sound ? 0 : 1,
                    "message_sound" => $settings->message_sound,
                    "created_at" => $settings->created_at,
                    "upated_at" => now()
                ]));

                $settings = json_decode(Redis::get('profiles:username:'.auth()->user()->uid));
                
                return response()->json(['code' => 200, 'title' => 'Notifications Sound Changed', 'message' => 'Notifications sound changed successfully', 'settings' => $settings]);
            } catch(\Exception $e) {
                session()->regenerate();
                return response()->json(['code' => $e->getStatusCode(), 'title' => 'Notification Sound Failure', 'message' => $e->getMessage()]);
            }
        }
    }

    protected function updateMessageSound (Request $request)
    {
        if (Auth::check()) {
            try {
                $settings = json_decode(Redis::get('profiles:username:'.auth()->user()->uid));
                Redis::set('profiles:username:'.auth()->user()->uid, json_encode([
                    "user_id" => auth()->user()->id,
                    "dark_mode" => $settings->dark_mode,
                    "notification_sound" => $settings->notification_sound,
                    "message_sound" => $settings->message_sound ? 0 : 1,
                    "created_at" => $settings->created_at,
                    "upated_at" => now()
                ]));
    
                $settings = json_decode(Redis::get('profiles:username:'.auth()->user()->uid));
                
                return response()->json(['code' => 200, 'title' => 'Messages Sound Changed', 'message' => 'Messages sound changed successfully!', 'settings' => $settings]);
            } catch(\Exception $e) {
                session()->regenerate();
                return response()->json(['code' => $e->getStatusCode(), 'title' => 'Messages Sound Failure', 'message' => $e->getMessage()]);
            }
        }
    }

    private function signoutUser ($request) {
        auth()->user()->tokens()->delete();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}