<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Jobs\AccountActivationJob;
use App\Jobs\VerificationResendJob;
use App\Models\Notification as Notifications;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use App\Notifications\TubaVerification;
use App\Http\Middleware\TrustHosts;
use Carbon\Carbon;

class VerificationController extends Controller
{

    use VerifiesEmails;

    protected $redirectTo = RouteServiceProvider::HOME;
    
    public function __construct()
    {
        $this->middleware(['auth', 'signed'])->only('verify');
        $this->middleware('throttle:5,1')->only('verify', 'resend');
    }

    public function view (Request $request) {
        $user = User::find(auth()->user()->id);
        if (!$user->hasVerifiedEmail()) {
            return view('auth.verify');
        }
        return redirect()->route('landing');
    }

    protected function resend (Request $request)
    {
        $user = User::find(auth()->user()->id);
        
        if (!$user->hasVerifiedEmail()) {
            VerificationResendJob::dispatch($user);
        }

        return back();
    }

    protected function verification ($id, $hash, Request $request)
    {
        $user = User::find(auth()->user()->id);

        abort_if( !hash_equals($hash, sha1($user->getEmailForVerification())), 403);

        if (!auth()->user()->hasVerifiedEmail())
        {
            $user->markEmailAsVerified();
            event(new Registered($user));

            $user = User::find(auth()->user()->id);
            
            AccountActivationJob::dispatch($user);
            
            $notifications = Notifications::where('user_id', $user->id)
            ->where('type', 'verification')
            ->get();

            foreach($notifications as $notification)
            {
                $notification->read_at = now();
                $notification->seen_at = now();
                $notification->save();
            }

            return redirect($this->redirectTo);
        }

        return back();
    }
}
