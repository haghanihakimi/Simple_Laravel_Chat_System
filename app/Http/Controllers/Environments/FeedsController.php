<?php

namespace App\Http\Controllers\Environments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Notification as Notifications;
use Carbon\Carbon;

class FeedsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    protected function index (Request $request)
    {
        $user = User::find(auth()->user()->id);
        
        $settings = json_decode(Redis::get('profiles:username:'.$user->uid));

        $replacedCode = $request->session()->get('replacedCode');

        $request->session()->remove('replacedCode');

        return view('layouts.feeds', compact(
            'user',
            'settings',
            'replacedCode'
        ));
    }
}
