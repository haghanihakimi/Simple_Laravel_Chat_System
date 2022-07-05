<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserUpdateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->public_uid,
            "username" => $this->username,
            "first_name" => $this->fname,
            "surname" => $this->sname,
            "privacy" => $this->is_locked,
            "gender" => $this->gender,
            "birthdate" => $this->bdate,
            "profile_picture" => $this->avatar,
            "bio" => $this->descriptions
        ];
    }
}
