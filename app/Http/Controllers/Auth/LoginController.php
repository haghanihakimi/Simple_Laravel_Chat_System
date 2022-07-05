<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;

class LoginController extends Controller
{

    use AuthenticatesUsers;
    
    protected $redirectTo = RouteServiceProvider::HOME;
    
    public function __construct()
    {
        $this->middleware(['guest', 'throttle:5,1'])->except('signout');
    }

    protected function index () {
        return view('layouts.login');
    }

    protected function signin (Request $request)
    {
        $this->validate($request, [
            "email" => "email|required|max:255|min:14",
            "password" => "required|max:255|min:8",
        ]);

        $users = User::withTrashed()->where('email', $request->email)->get();

        if (count($users) > 0 || !empty($users)) {
            foreach ($users as $user) {
                if (Hash::check($request->password, $user->password)) {
                    if ($user->is_restorable && !empty($user->deleted_at) && empty($user->status)) {
                        $user->update([
                            'deleted_at' => null,
                            'is_restorable' => null
                        ]);
                        $user->save();
                    
                        return $this->twoFaChallenge($request, $user);
                    }
                    
                    if (!$user->is_restorable && !empty($user->deleted_at)) {
                        return back()->with('status', __('auth.deleted'));
                    }
            
                    if ($user->is_restorable && !empty($user->deleted_at) && $user->status === 'suspended') {
                        return back()->with('status', __('auth.suspended'));
                    }
                    
                    if (empty($user->is_restorable) && empty($user->deleted_at)) {
                        return $this->twoFaChallenge($request, $user);
                    }
                }
                return back()->with('status', __('auth.failed'));
            }
        }

        return back()->with('status', __('auth.failed'));
    }

    private function twoFaChallenge ($request, $user) {
        if (!empty($user->two_factor_secret)) {
            $request->session()->put('loginChallenge', collect(['id' => $user->id, 'time' => now()]));
            $request->session()->put('twoFaFailed', 0);

            return redirect()->route('twofa.challenge');
        }

        if (!auth()->attempt($request->only('email', 'password'), $request->rememberme)) {
            return back()->with('status', __('auth.failed'));
        }

        return redirect($this->redirectTo);
    }

    protected function signout (Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return response()->json([
            'code' => 200,
            'signature' => 'succeeded'
        ]);
    }
}