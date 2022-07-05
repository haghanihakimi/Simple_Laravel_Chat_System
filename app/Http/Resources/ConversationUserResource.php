<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConversationUserResource extends JsonResource
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
            "profile_picture" => $this->avatar,
            "conversation_id" => $this->pivot->public_id,
            "created_at" => $this->pivot->created_at,
            "updated_at" => $this->pivot->updated_at,
            "current_page" => $this->current_page,
            "first_page_url" => $this->first_page_url,
            "from" => $this->from,
            "last_page" => $this->last_page,
            "last_page_url" => $this->last_page_url,
            "links" => $this->links
        ];
    }
}
