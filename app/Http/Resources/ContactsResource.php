<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContactsResource extends JsonResource
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
            "uid" => $this->public_uid,
            "username" => $this->username,
            "first_name" => $this->fname,
            "surname" => $this->sname,
            "profile_image" => $this->avatar,
            "friend_since" => $this->pivot->updated_at,
            'userMessageable' => auth()->user()->checkMessageable($this),
            'requestRejectable' => auth()->user()->checkRejectable($this),
            'requestAcceptable' => auth()->user()->checkAcceptable($this),
            'requestCancellable' => auth()->user()->checkCancellable($this),
            'userRemovable' => auth()->user()->checkContactRemovable($this),
            'userSpamMarkable' => auth()->user()->checkSpamMarkable($this),
            'userBlockable' => auth()->user()->checkBlockable($this)
        ];
    }
}
