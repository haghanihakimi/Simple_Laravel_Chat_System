<?php

namespace App\Http\Controllers\Environments;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helpers\RedisController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserUpdateResource;
use App\Http\Resources\PeopleSearchResource;
use App\Http\Resources\SearchResultUserResource;
use App\Models\User;
use App\Models\Block;
use App\Models\Contact;
use Carbon\Carbon;

class UserController extends Controller
{
    public function __construct ()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function collectUserData ()
    {
        try {
            $user = User::find(auth()->user()->id);

            $user = new UserUpdateResource($user);

            return response()->json(['code' => 200, 'title' => 'User Profile Loading Success', 'message' => 'User Profile loaded successfully', 'profiles' => $user]);
        } catch (\Exception $e)
        {
            return response()->json(['code' => 500, 'title' => 'User Profile Loading Failure', 'message' => $e->getMessage()]);
        }
    }

    protected function saveChnages (Request $request) {
        session()->regenerate();
        $user = [];
        
        $this->validation($request);

        $user = User::find(auth()->user()->id);

        $user->update([
            "username" => $user->username,
            "fname" => (!empty($request->fname)) ? $request->fname : $user->fname,
            "sname" => (!empty($request->sname)) ? $request->sname : $user->sname,
            "is_locked" => (!empty($request->privacy) || $request->privacy !== $user->is_locked) ? $request->privacy : $user->is_locked,
            "gender" => (!empty($request->gender)) ? $request->gender : $user->gender,
            "bdate" => !empty($request->dob) ? Carbon::parse($request->dob)->format('Y-m-d') : $user->bdate,
            "avatar" => (!empty($request->avatar)) ? $request->avatar : $user->avatar,
            "descriptions" => (!empty($request->descriptions)) ? $request->descriptions : $user->descriptions
        ]);
        
        $user = User::find(auth()->user()->id);

        $user = new UserUpdateResource($user);
        
        return response()->json(['code' => 200, 'title' => 'Chnages Saved', 'message' => 'Profile changes saved successfully!', 'profiles' => $user]);    
    }

    protected function collectPeopleSearch (Request $request) {
        try {
            $this->searchValidation($request);

            $query = $request->query('keywords');
            
            $users = User::select('id', 'public_uid', 'username', 'fname', 'sname', 'avatar')
            ->where(DB::raw("concat(fname, ' ' , sname)"), 'LIKE', '%' . $query . '%')
            ->orWhere('username', 'LIKE', '%' . $query . '%')
            ->orderBy('fname', 'ASC')
            ->limit(9)
            ->get();

            $profiles = [];

            for ($i = 0;$i < count($users);$i++) {
                if ($users[$i]->public_uid !== auth()->user()->public_uid && !auth()->user()->checkBeingBlocked($users[$i])) {
                    $profiles[] = new PeopleSearchResource($users[$i]);
                }
            }
            
            return response()->json(['code' => 200, 'title' => 'Loading Profiles', 'message' => 'Profiles loaded successfully', 'people' => $profiles]);    
        } catch (\Exception $e)
        {
            return response()->json(['code' => 500, 'title' => 'User Profiles Loading Failure', 'message' => $e->getMessage(), 'token' => csrf_token()]);
        }
    }

    protected function collectSearchedUser ($id, Request $request) {
        try {
            $user = Auth::user();
            //$blockedUsers = $user->blocks->pluck('blocked_id')->toArray();

            $this->userIdValidation($request);
            $target = User::where('public_uid', $request->id)->firstOrFail();
            $profile = [];

            $profile[] = new SearchResultUserResource($target);
            
            return response()->json([
                'code' => 200, 
                'title' => 'Loading Profile', 
                'message' => 'Profile loaded successfully', 
                'user' => $profile,
                'userAddable' => $user->checkAddable($target),
                'userMessageable' => $user->checkMessageable($target),
                'requestRejectable' => $user->checkRejectable($target),
                'requestCancellable' => $user->checkCancellable($target),
                'requestAcceptable' => $user->checkAcceptable($target),
                'contactRemovable' => $user->checkContactRemovable($target),
                'contactBocklable' => $user->checkBlockable($target),
                'markContactSpam' => $user->checkSpamMarkable($target),
                'markedAsSpam' => $user->checkHasSpammed($target),
            ]);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'title' => 'User Profile Loading Failure', 'message' => $e->getMessage()]);
        }
    }

    private function validation ($request) 
    {
        return $this->validate($request, [
            'fname' => ['nullable', 'string', 'regex:/^[a-zA-Z]+$/u', 'min:3', 'max:64'],
            'sname' => ['nullable', 'string', 'regex:/^[a-zA-Z]+$/u', 'min:3', 'max:64'],
            'is_locked' => ['nullable', 'boolean', 'regex:/^[0-1]+$/u'],
            'descriptions' => ['nullable', 'string', 'min:3', 'max:500'],
            'gender' => ['nullable', 'in:female,male', 'regex:/^[female]+$/u'],
            'dob' => ['nullable', 'date', 'before:14 years ago', 'after: 200 years ago'],
        ]);
    }

    private function userIdValidation ($request) 
    {
        return $this->validate($request, [
            'id' => ['nullable', 'numeric', 'regex:/^[0-9]+$/u'],
        ]);
    }

    private function searchValidation ($request) 
    {
        return $this->validate($request, [
            'keywords' => ['nullable', 'string', "regex:/^[a-zA-Z0-9' ]+$/u"],
        ]);
    }
}
