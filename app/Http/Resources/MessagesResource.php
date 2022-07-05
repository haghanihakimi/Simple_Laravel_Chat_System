<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class MessagesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    protected $sender;

    public function sender ($input) {
        $this->target = $input;
        return $this;
    }


    public function toArray($request)
    {
        return [
            "sent" => $this->sender,
            "message_id" => $this->public_id,
            "message" => Crypt::decryptString($this->message),
            "timestamp" => Carbon::parse($this->timestamp)->format("D, d M 'y - h:m A"),
        ];
    }
}
