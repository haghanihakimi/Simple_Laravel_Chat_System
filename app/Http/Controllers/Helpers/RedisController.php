<?php

namespace App\Http\Controllers\Helpers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

class RedisController extends Controller
{

    public static function getProfile ($id, $bdateType) {
        $user = json_decode(Redis::get('profiles:username:'.$id));

        switch ($bdateType) {
            case "short":
                $user = (object) collect([
                    "id" => $user->id,
                    "username" => $user->username,
                    "first_name" => $user->first_name,
                    "surname" => $user->surname,
                    "privacy" => $user->privacy,
                    "status" => $user->status,
                    "gender" => $user->gender,
                    "birthdate" => Carbon::parse($user->birthdate)->diff(now())->y,
                    "profile_picture" => $user->profile_picture,
                    "bio" => $user->bio
                ])->all();
                break;
            case "full":
                $user = (object) collect([
                    "id" => $user->id,
                    "username" => $user->username,
                    "first_name" => $user->first_name,
                    "surname" => $user->surname,
                    "privacy" => $user->privacy,
                    "status" => $user->status,
                    "gender" => $user->gender,
                    "birthdate" => $user->birthdate,
                    "profile_picture" => $user->profile_picture,
                    "bio" => $user->bio,
                ])->all();
                break;
            default:
                $user = (object) collect([
                    "id" => $user->id,
                    "username" => $user->username,
                    "first_name" => $user->first_name,
                    "surname" => $user->surname,
                    "privacy" => $user->privacy,
                    "status" => $user->status,
                    "gender" => $user->gender,
                    "birthdate" => $user->birthdate,
                    "profile_picture" => $user->profile_picture,
                    "bio" => $user->bio
                ])->all();
                break;
        }

        return $user;
    }

    public static function updateUser ($id, array $inputs) {
        $user = json_decode(Redis::get('profiles:username:'.$id));


    }
}
