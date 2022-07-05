<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Redis;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Jobs\RegistrationJob;
use Carbon\Carbon;

class RegisterController extends Controller
{

    use RegistersUsers;
    
    protected $redirectTo = RouteServiceProvider::HOME;
    
    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function index ()
    {
        $carbon = Carbon::class;
        $months = Carbon::now()->format('m') - 1;
        return view('layouts.signup', compact(
            'carbon',
            'months'
        ));
    }

    protected function create(Request $request)
    {
        $this->checkInputs($request);

        $user = $this->store($request);

        RegistrationJob::dispatch($user);

        auth()->attempt($request->only('email', 'password'), 'on');

        auth()->user()->createToken(auth()->user()->username, ['server:update', 'server:read', 'server:delete'])->plainTextToken;
        
        return redirect($this->redirectTo)->with('registered', 'Thank you for signin up with us! Please verify your account');
    }

    private function url_generator ($user)
    {
        $url = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 120)),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification())
            ]
        );

        return $url;
    }

    private function checkInputs ($request) 
    {
        return $this->validate($request, [
            'fname' => ['required', 'string', 'min:4', 'max:255'],
            'sname' => ['required', 'string', 'min:4', 'max:255'],
            'email' => ['required', 'string', 'email', 'confirmed', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'birthdate' => ['required', 'date', 'before:15 years ago', 'after: 200 years ago'],
            'gender' => ['required', 'in:female,male']
        ]);
    }

    private function store ($request)
    {
        $user = User::firstOrCreate([
            'uid' => sha1(Str::uuid()->toString()),
            'public_uid' => sha1(Str::uuid()->toString()),
            'username' => substr(Str::uuid()->toString(), 0,16),
            'fname' => ucwords(strtolower($request->fname)),
            'sname' => ucwords(strtolower($request->sname)),
            'email' => $request->email,
            'password' => Hash::make($request->password, [
                'memory' => 4096,
                'time' => 24,
                'threads' => 48
            ]),
            'phone' => null,
            'gender' => $request->gender,
            'bdate' => $request->birthdate,
            'is_active' => true
        ]);
        
        Redis::set('profiles:username:'.$user->uid, json_encode([
            "user_id" => $user->public_uid,
            "dark_mode" => 0,
            "notification_sound" => 1,
            "message_sound" => 1,
            "created_at" => now(),
            "upated_at" => now()
        ]));
        Redis::set('user:profile:'.$user->uid, 
            json_encode([
                "id" => $user->public_uid,
                "first_name" => $user->fname,
                "surname" => $user->sname,
                "privacy" => false,
                "status" => true,
                "gender" => $user->gender,
                "birthdate" => $user->bdate,
                "profile_picture" => $user->avatar,
                "bio" => $user->descriptions
            ])
        );

        event(new Registered($user));
    
        return $user;
    }
}
