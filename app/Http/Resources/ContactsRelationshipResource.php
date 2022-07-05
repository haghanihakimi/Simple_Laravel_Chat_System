<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContactsRelationshipResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    protected $target;

    public function target ($input) {
        $this->target = $input;
        return $this;
    }

    public function toArray($request)
    {
        return [
            "add" => $this->checkAddable($this->target),
            "message" => $this->checkMessageable($this->target),
            "remove" => $this->checkContactRemovable($this->target),
            "accept" => $this->checkAcceptable($this->target),
            "reject" => $this->checkRejectable($this->target),
            "cancel" => $this->checkCancellable($this->target),
            "block" => $this->checkBlockable($this->target),
            "spam" => $this->checkSpamMarkable($this->target),
            "hasSpammed" => $this->checkHasSpammed($this->target),
        ];
    }
}
